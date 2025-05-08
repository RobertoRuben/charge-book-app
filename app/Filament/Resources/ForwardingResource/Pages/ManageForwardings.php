<?php

namespace App\Filament\Resources\ForwardingResource\Pages;

use App\Filament\Resources\ForwardingResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageForwardings extends ManageRecords
{
    protected static string $resource = ForwardingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
