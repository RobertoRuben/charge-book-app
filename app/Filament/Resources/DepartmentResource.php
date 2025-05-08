<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepartmentResource\Pages;
use App\Filament\Resources\DepartmentResource\RelationManagers;
use App\Models\Department;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Empresa';

    protected static ?string $modelLabel = 'Área Departamental';

    protected static ?string $pluralModelLabel = 'Áreas Departamentales';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Departamento')
                    ->description('Información sobre el área departamental')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->unique()
                            ->minLength(3)
                            ->label('Nombre del Área')
                            ->maxLength(255)
                            ->placeholder('Ingrese el nombre del área')
                            ->regex('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-_,.&()]+$/')
                            ->validationAttribute('nombre del área')
                            ->helperText('El nombre del área debe contener solo letras y espacios'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre del Área')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime('d/m/Y H:i')
                    ->timezone('America/Lima')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Fecha de Actualización')
                    ->dateTime('d/m/Y H:i')
                    ->timezone('America/Lima')
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
            'index' => Pages\ManageDepartments::route('/'),
        ];
    }
}
