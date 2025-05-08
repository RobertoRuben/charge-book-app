<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'Usuario';

    protected static ?string $pluralModelLabel = 'Usuarios';

    protected static ?string $navigationGroup = 'Sistema';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de Usuario')
                    ->description('Datos básicos de acceso al sistema')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre de Usuario')
                            ->required()
                            ->unique()
                            ->minLength(2)
                            ->maxLength(255)
                            ->placeholder('Ingrese nombre de usuario'),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->unique()
                            ->required()
                            ->minLength(2)
                            ->maxLength(255)
                            ->regex('/^[a-zA-Z0-9._%+-]+@mda\.gob\.pe$/')
                            ->validationAttribute('correo electrónico')
                            ->placeholder('usuario@mda.gob.pe'),
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->timezone('America/Lima')
                            ->placeholder('Seleccione fecha y hora'),
                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->required(fn($record) => !$record)
                            ->dehydrated(fn($state) => filled($state))
                            ->regex('/^(?=.*[A-Z])(?=.*[0-9]).{8,}$/')
                            ->helperText('Debe contener al menos 8 caracteres, una mayúscula y un número')
                            ->minLength(8)
                            ->maxLength(255)
                            ->placeholder('Ingrese contraseña'),
                    ])->columns(2),

                Forms\Components\Section::make('Asociar con Empleado')
                    ->description('Vincula este usuario con un empleado existente')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label('Empleado')
                            ->required()
                            ->relationship('employee', 'id')
                            ->getOptionLabelFromRecordUsing(fn(Employee $record) => $record->full_name)
                            ->searchable(['last_name', 'middle_name', 'first_names'])
                            ->preload()
                            ->placeholder('Seleccione un empleado')
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre de Usuario')
                    ->searchable(),
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Empleado')
                    ->searchable(['employee.last_name', 'employee.middle_name', 'employee.first_names'])
                    ->sortable()
                    ->placeholder('Sin empleado asociado'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('Verificado')
                    ->dateTime('d/m/Y H:i')
                    ->timezone('America/Lima')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Fecha desde')
                            ->placeholder('DD/MM/YYYY')
                            ->displayFormat('d/m/Y')
                            ->timezone('America/Lima'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Fecha hasta')
                            ->placeholder('DD/MM/YYYY')
                            ->displayFormat('d/m/Y')
                            ->timezone('America/Lima'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Creado desde ' . \Carbon\Carbon::parse($data['created_from'])->format('d/m/Y');
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Creado hasta ' . \Carbon\Carbon::parse($data['created_until'])->format('d/m/Y');
                        }

                        return $indicators;
                    }),

                Tables\Filters\SelectFilter::make('employee_id')
                    ->label('Empleado')
                    ->relationship('employee', 'last_name')
                    ->getOptionLabelFromRecordUsing(fn (Employee $record) => $record->full_name)
                    ->searchable()
                    ->preload()
                    ->placeholder('Seleccione un empleado'),
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
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
