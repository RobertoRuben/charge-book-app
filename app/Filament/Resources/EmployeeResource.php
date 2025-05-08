<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use App\Models\Department;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Empresa';

    protected static ?string $modelLabel = 'Trabajador';

    protected static ?string $pluralModelLabel = 'Trabajadores';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Personal')
                    ->description('Datos personales del trabajador')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Forms\Components\TextInput::make('dni')
                            ->label('DNI')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->regex('/^[0-9]{8}$/')
                            ->minLength(8)
                            ->maxLength(8)
                            ->placeholder('Ingrese el DNI del trabajador')
                            ->validationAttribute('DNI')
                            ->helperText('El DNI debe tener 8 dígitos'),
                        Forms\Components\TextInput::make('last_name')
                            ->label('Apellido Paterno')
                            ->required()
                            ->regex('/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/')
                            ->maxLength(100)
                            ->placeholder('Ingrese el apellido paterno'),
                        Forms\Components\TextInput::make('middle_name')
                            ->label('Apellido Materno')
                            ->required()
                            ->regex('/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/')
                            ->maxLength(100)
                            ->placeholder('Ingrese el apellido materno'),
                        Forms\Components\TextInput::make('first_names')
                            ->label('Nombres')
                            ->required()
                            ->regex('/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/')
                            ->maxLength(100)
                            ->placeholder('Ingrese los nombres'),
                    ])->columns(2),

                Forms\Components\Section::make('Información Laboral')
                    ->description('Datos laborales del trabajador')
                    ->icon('heroicon-o-briefcase')
                    ->schema([
                        Forms\Components\Select::make('department_id')
                            ->label('Área Departamental')
                            ->options(Department::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Seleccione el área departamental')
                            ->relationship('department', 'name'),
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
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('dni')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Apellido Paterno')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('middle_name')
                    ->label('Apellido Materno')
                    ->searchable(),
                Tables\Columns\TextColumn::make('first_names')
                    ->label('Nombres')
                    ->searchable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->label('Área Departamental')
                    ->sortable(),
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
                Tables\Filters\SelectFilter::make('department_id')
                    ->label('Área Departamental')
                    ->options(Department::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->relationship('department', 'name')
                    ->placeholder('Seleccione un área'),
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
            'index' => Pages\ManageEmployees::route('/'),
        ];
    }
}
