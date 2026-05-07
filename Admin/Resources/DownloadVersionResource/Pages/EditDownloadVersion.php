<?php

declare(strict_types=1);

namespace Paymenter\Extensions\Servers\DownloadableProducts\Admin\Resources\DownloadVersionResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Paymenter\Extensions\Servers\DownloadableProducts\Admin\Resources\DownloadVersionResource;

class EditDownloadVersion extends EditRecord
{
    protected static string $resource = DownloadVersionResource::class;
}
