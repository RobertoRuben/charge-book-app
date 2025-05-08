<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ForwardingResource\Pages;
use App\Filament\Resources\ForwardingResource\RelationManagers;
use App\Models\Forwarding;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ForwardingResource extends Resource
{
    protected static ?string $model = Forwarding::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Cargo';

    protected static ?string $pluralModelLabel = 'Cargos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('document_id')
                    ->relationship('document', 'title')
                    ->required(),
                Forms\Components\Select::make('origin_department_id')
                    ->relationship('originDepartment', 'name')
                    ->required(),
                Forms\Components\Select::make('destination_department_id')
                    ->relationship('destinationDepartment', 'name')
                    ->required(),
                Forms\Components\DateTimePicker::make('forwarding_date')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('document.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('originDepartment.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('destinationDepartment.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('forwarding_date')
                    ->dateTime()
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
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageForwardings::route('/'),
        ];
    }
}
