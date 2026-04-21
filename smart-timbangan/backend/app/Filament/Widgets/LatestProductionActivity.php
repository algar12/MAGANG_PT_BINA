<?php

namespace App\Filament\Widgets;

use App\Models\ProductionCosting;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestProductionActivity extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Aktivitas Timbangan Terakhir (Live)';
    protected static ?string $pollingInterval = '2s';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProductionCosting::query()->latest('updated_at')->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Waktu')
                    ->dateTime()
                    ->since(),
                Tables\Columns\TextColumn::make('productionOrder.order_number')
                    ->label('No. Order'),
                Tables\Columns\TextColumn::make('bomItem.material.nama_produk')
                    ->label('Produk'),
                Tables\Columns\TextColumn::make('netto_target')
                    ->label('Target (kg)')
                    ->numeric(),
                Tables\Columns\TextColumn::make('netto_produksi')
                    ->label('Aktual (kg)')
                    ->numeric()
                    ->weight('bold')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'gray',
                        'Weighed' => 'success',
                        'Approved' => 'primary',
                    }),
            ]);
    }
}
