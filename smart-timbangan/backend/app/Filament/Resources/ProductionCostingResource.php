<?php

namespace App\Filament\Resources;

use App\Filament\Exports\ProductionCostingExporter;
use App\Filament\Resources\ProductionCostingResource\Pages;
use App\Models\BomItem;
use App\Models\Device;
use App\Models\Material;
use App\Models\ProductionOrder;
use App\Models\ProductionCosting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductionCostingResource extends Resource
{
    protected static ?string $model = ProductionCosting::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationGroup = 'PENIMBANGAN UTAMA';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'MULAI MENIMBANG';
    protected static ?string $pluralModelLabel = 'MULAI MENIMBANG';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Placeholder::make('nomor_otomatis')
                    ->label('Nomor')
                    ->content(fn (?ProductionCosting $record): string => $record?->productionOrder?->order_number ?? 'Otomatis setelah disimpan'),
                Forms\Components\Select::make('material_id')
                    ->label('Bahan Baku')
                    ->options(fn (): array => Material::query()
                        ->where('is_active', true)
                        ->orderBy('nama_produk')
                        ->pluck('nama_produk', 'id')
                        ->all())
                    ->afterStateHydrated(function (Forms\Components\Select $component, ?ProductionCosting $record): void {
                        $component->state($record?->bomItem?->material_id);
                    })
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('device_id')
                    ->relationship('device', 'name')
                    ->getOptionLabelFromRecordUsing(fn (Device $record): string => $record->name ?: $record->device_id ?: "Device #{$record->id}"),
                Forms\Components\TextInput::make('netto_produksi')
                    ->numeric(),
                Forms\Components\Select::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'Weighed' => 'Weighed',
                        'Approved' => 'Approved',
                    ])
                    ->default('Pending')
                    ->required(),
                Forms\Components\DateTimePicker::make('weighed_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('productionOrder.order_number')
                    ->label('Nomor')
                    ->size(TextColumnSize::Small)
                    ->sortable(),
                Tables\Columns\TextColumn::make('bomItem.material.nama_produk')
                    ->label('Nama Produk')
                    ->size(TextColumnSize::Small)
                    ->limit(24)
                    ->sortable(),
                Tables\Columns\TextColumn::make('netto_produksi')
                    ->label('Netto Aktual (Live)')
                    ->numeric()
                    ->size(TextColumnSize::Small)
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('uom_display')
                    ->label('UOM')
                    ->size(TextColumnSize::Small),
                Tables\Columns\TextColumn::make('status')
                    ->size(TextColumnSize::ExtraSmall)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'gray',
                        'Weighed' => 'success',
                        'Approved' => 'primary',
                    }),
                Tables\Columns\TextColumn::make('weighed_at')
                    ->dateTime()
                    ->size(TextColumnSize::Small)
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->size(TextColumnSize::Small)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->size(TextColumnSize::Small)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->poll('2s') // Ini membuat tabel live update tiap 2 detik!
            ->headerActions([
                Tables\Actions\ExportAction::make('export_today')
                    ->label('Export Hari Ini')
                    ->exporter(ProductionCostingExporter::class)
                    ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereDate('created_at', today())),
                Tables\Actions\ExportAction::make('export_this_month')
                    ->label('Export Bulan Ini')
                    ->exporter(ProductionCostingExporter::class)
                    ->modifyQueryUsing(fn (Builder $query): Builder => $query
                        ->whereYear('created_at', now()->year)
                        ->whereMonth('created_at', now()->month)),
                Tables\Actions\ExportAction::make('export_this_year')
                    ->label('Export Tahun Ini')
                    ->exporter(ProductionCostingExporter::class)
                    ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereYear('created_at', now()->year)),
            ])
            ->filters([
                Tables\Filters\Filter::make('periode_data')
                    ->label('Periode Data')
                    ->form([
                        Forms\Components\Select::make('periode')
                            ->label('Tampilkan')
                            ->options([
                                'today' => 'Hari Ini',
                                'this_month' => 'Bulan Ini',
                                'this_year' => 'Tahun Ini',
                            ])
                            ->placeholder('Semua Data'),
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $query = match ($data['periode'] ?? null) {
                            'today' => $query->whereDate('created_at', today()),
                            'this_month' => $query
                                ->whereYear('created_at', now()->year)
                                ->whereMonth('created_at', now()->month),
                            'this_year' => $query->whereYear('created_at', now()->year),
                            default => $query,
                        };

                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ExportBulkAction::make()
                        ->label('Export Excel Terpilih')
                        ->exporter(ProductionCostingExporter::class),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductionCostings::route('/'),
            'create' => Pages\CreateProductionCosting::route('/create'),
            'edit' => Pages\EditProductionCosting::route('/{record}/edit'),
        ];
    }
}
