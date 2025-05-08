<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Filament\Resources\DocumentResource\RelationManagers;
use App\Models\Document;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Documentos';

    protected static ?string $modelLabel = 'Documento';

    protected static ?string $pluralModelLabel = 'Documentos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del documento')
                    ->description('Información básica del documento')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ingrese el título del documento')
                            ->columnSpan(2),
                        Forms\Components\Textarea::make('subject')
                            ->label('Asunto')
                            ->required()
                            ->placeholder('Ingrese el asunto del documento')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('page_count')
                            ->label('Número de páginas')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->placeholder('Ingrese el número de páginas')
                            ->suffix('páginas')
                            ->helperText('Cantidad de páginas que contiene el documento'),
                    ])->columns(2),

                Forms\Components\Section::make('Remitente')
                    ->description('Seleccione el trabajdor que ingresa el documento)')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label('Empleado')
                            ->relationship('employee', 'last_name')
                            ->getOptionLabelFromRecordUsing(fn(Employee $record) => $record->full_name)
                            ->searchable()
                            ->preload()
                            ->placeholder('Seleccione un empleado (opcional)')
                            ->nullable(),
                    ]),

                Forms\Components\Section::make('Archivo del documento')
                    ->schema([
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Archivo')
                            ->disk('public')
                            ->directory('documents')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(10240)
                            ->downloadable()
                            ->previewable()
                            ->preserveFilenames()
                            ->helperText('Solo se permiten archivos PDF de máximo 10MB')
                            ->columnSpanFull()
                            ->visibility('public'),
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
                Tables\Columns\TextColumn::make('formatted_registry_number')
                    ->label('Nº de registro')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('registry_number', $direction);
                    }),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),
                Tables\Columns\TextColumn::make('page_count')
                    ->label('Páginas')
                    ->sortable(),
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Ingresado Por')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->timezone('America/Lima')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->timezone('America/Lima')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('registry_number')
                    ->form([
                        Forms\Components\TextInput::make('registry_number_from')
                            ->label('Desde nº')
                            ->numeric(),
                        Forms\Components\TextInput::make('registry_number_to')
                            ->label('Hasta nº')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['registry_number_from'],
                                fn(Builder $query, $registryNumber): Builder => $query->where('registry_number', '>=', $registryNumber),
                            )
                            ->when(
                                $data['registry_number_to'],
                                fn(Builder $query, $registryNumber): Builder => $query->where('registry_number', '<=', $registryNumber),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['registry_number_from'] ?? null) {
                            $indicators['registry_number_from'] = 'Registro desde: ' . $data['registry_number_from'];
                        }

                        if ($data['registry_number_to'] ?? null) {
                            $indicators['registry_number_to'] = 'Registro hasta: ' . $data['registry_number_to'];
                        }

                        return $indicators;
                    }),

                SelectFilter::make('employee_id')
                    ->label('Empleado')
                    ->relationship('employee', 'last_name', fn(Builder $query) => $query->orderBy('last_name'))
                    ->getOptionLabelFromRecordUsing(fn(Employee $record) => $record->full_name)
                    ->searchable()
                    ->preload(),

                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Fecha desde'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Fecha hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Desde: ' . $data['created_from'];
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Hasta: ' . $data['created_until'];
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(

                ),
                Tables\Actions\Action::make('preview')
                    ->label('Descargar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn(Document $record) => Storage::disk('public')->url($record->file_path))
                    ->openUrlInNewTab()
                    ->visible(fn(Document $record) => !empty($record->file_path)),
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
            'index' => Pages\ManageDocuments::route('/'),
        ];
    }
}
