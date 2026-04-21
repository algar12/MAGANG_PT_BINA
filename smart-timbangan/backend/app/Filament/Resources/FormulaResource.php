<?php

namespace App\Filament\Resources;

use App\Filament\Exports\FormulaExporter;
use App\Filament\Resources\FormulaResource\Pages;
use App\Models\Material;
use App\Models\Formula;
use Filament\Forms;
use Illuminate\Support\Str;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FormulaResource extends Resource
{
    protected static ?string $model = Formula::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationGroup = 'PENGATURAN SISTEM';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Resep / Formula';
    protected static ?string $pluralModelLabel = 'Resep / Formula';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('formula_code')
                    ->label('Kode Formula')
                    ->default('FRM-' . strtoupper(Str::random(6)))
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('formula_name')
                    ->required()
                    ->maxLength(150),
                Forms\Components\TextInput::make('mix_kategory')
                    ->maxLength(50),
                Forms\Components\Toggle::make('status')
                    ->default(true),
                
                Forms\Components\Section::make('Bill of Materials (BOM)')
                    ->schema([
                        Forms\Components\Repeater::make('bomItems')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('material_id')
                                    ->relationship('material', 'nama_produk')
                                    ->getOptionLabelFromRecordUsing(fn (Material $record): string => $record->nama_produk ?: $record->kode_produk ?: "Material #{$record->id}")
                                    ->required()
                                    ->searchable(),
                                Forms\Components\TextInput::make('bom_konversi_qty')
                                    ->numeric()
                                    ->default(1.00)
                                    ->required(),
                                Forms\Components\TextInput::make('bom_konversi_uom')
                                    ->required()
                                    ->maxLength(20),
                                Forms\Components\TextInput::make('netto_target')
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('mix_id')
                                    ->maxLength(50),
                                Forms\Components\Toggle::make('is_optional')
                                    ->default(false),
                            ])
                            ->columns(3)
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('formula_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('formula_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mix_kategory')
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->label('Export Excel')
                    ->exporter(FormulaExporter::class),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ExportBulkAction::make()
                        ->label('Export Excel Terpilih')
                        ->exporter(FormulaExporter::class),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListFormulas::route('/'),
            'create' => Pages\CreateFormula::route('/create'),
            'edit' => Pages\EditFormula::route('/{record}/edit'),
        ];
    }
}
