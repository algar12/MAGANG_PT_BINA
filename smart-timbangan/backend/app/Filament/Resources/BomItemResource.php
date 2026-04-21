<?php

namespace App\Filament\Resources;

use App\Filament\Exports\BomItemExporter;
use App\Filament\Resources\BomItemResource\Pages;
use App\Models\BomItem;
use App\Models\Material;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BomItemResource extends Resource
{
    protected static ?string $model = BomItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'PENGATURAN SISTEM';
    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel = 'BOM Item';
    protected static ?string $pluralModelLabel = 'BOM Item';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('material_id')
                    ->label('Bahan Baku')
                    ->relationship('material', 'nama_produk')
                    ->getOptionLabelFromRecordUsing(fn (Material $record): string => $record->nama_produk ?: $record->kode_produk ?: "Material #{$record->id}")
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('netto_target')
                    ->label('Netto Target')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('bom_konversi_uom')
                    ->label('Satuan')
                    ->default('KG')
                    ->placeholder('Contoh: KG')
                    ->maxLength(20)
                    ->required(),
                Forms\Components\Toggle::make('is_optional')
                    ->label('Opsional')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('material.nama_produk')
                    ->label('Bahan Baku')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('netto_target')
                    ->label('Netto Target')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bom_konversi_uom')
                    ->label('Satuan')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_optional')
                    ->label('Opsional')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make('export_today')
                    ->label('Export Hari Ini')
                    ->exporter(BomItemExporter::class)
                    ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereDate('created_at', today())),
                Tables\Actions\ExportAction::make('export_this_month')
                    ->label('Export Bulan Ini')
                    ->exporter(BomItemExporter::class)
                    ->modifyQueryUsing(fn (Builder $query): Builder => $query
                        ->whereYear('created_at', now()->year)
                        ->whereMonth('created_at', now()->month)),
                Tables\Actions\ExportAction::make('export_this_year')
                    ->label('Export Tahun Ini')
                    ->exporter(BomItemExporter::class)
                    ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereYear('created_at', now()->year)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ExportBulkAction::make()
                        ->label('Export Excel Terpilih')
                        ->exporter(BomItemExporter::class),
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
            'index' => Pages\ListBomItems::route('/'),
            'create' => Pages\CreateBomItem::route('/create'),
            'edit' => Pages\EditBomItem::route('/{record}/edit'),
        ];
    }
}
