<?php

namespace App\Filament\Resources;

use App\Filament\Exports\ProductionOrderExporter;
use App\Filament\Resources\ProductionOrderResource\Pages;
use App\Models\Formula;
use App\Models\ProductionOrder;
use App\Models\User;
use Filament\Forms;
use Illuminate\Support\Str;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductionOrderResource extends Resource
{
    protected static ?string $model = ProductionOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'PENGATURAN SISTEM';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Rencana Kerja';
    protected static ?string $pluralModelLabel = 'Rencana Kerja';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('order_number')
                    ->label('Nomor Order')
                    ->default('PO-' . date('Ymd') . '-' . strtoupper(Str::random(4)))
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(50),
                Forms\Components\Select::make('formula_id')
                    ->relationship('formula', 'formula_name')
                    ->getOptionLabelFromRecordUsing(fn (Formula $record): string => $record->formula_name ?: $record->formula_code ?: "Formula #{$record->id}")
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('qty_order')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\DatePicker::make('start_date')
                    ->required(),
                Forms\Components\DatePicker::make('end_date'),
                Forms\Components\Select::make('status')
                    ->options([
                        'Draft' => 'Draft',
                        'In Progress' => 'In Progress',
                        'Completed' => 'Completed',
                        'Cancelled' => 'Cancelled',
                    ])
                    ->default('Draft')
                    ->required(),
                Forms\Components\Select::make('operator_id')
                    ->relationship('operator', 'name')
                    ->getOptionLabelFromRecordUsing(fn (User $record): string => $record->name ?: $record->email ?: "User #{$record->id}")
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('formula.formula_name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('qty_order')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('operator_id')
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
                    ->exporter(ProductionOrderExporter::class),
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
                        ->exporter(ProductionOrderExporter::class),
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
            'index' => Pages\ListProductionOrders::route('/'),
            'create' => Pages\CreateProductionOrder::route('/create'),
            'edit' => Pages\EditProductionOrder::route('/{record}/edit'),
        ];
    }
}
