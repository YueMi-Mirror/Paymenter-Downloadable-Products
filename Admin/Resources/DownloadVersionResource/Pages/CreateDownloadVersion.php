<?php

declare(strict_types=1);

namespace Paymenter\Extensions\Servers\DownloadableProducts\Admin\Resources\DownloadVersionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Paymenter\Extensions\Servers\DownloadableProducts\Admin\Resources\DownloadVersionResource;

class CreateDownloadVersion extends CreateRecord
{
    protected static string $resource = DownloadVersionResource::class;
}
