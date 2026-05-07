<?php

declare(strict_types=1);

namespace Paymenter\Extensions\Servers\DownloadableProducts\Admin\Resources\DownloadVersionResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Paymenter\Extensions\Servers\DownloadableProducts\Admin\Resources\DownloadVersionResource;

class ListDownloadVersions extends ListRecords
{
    protected static string $resource = DownloadVersionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
