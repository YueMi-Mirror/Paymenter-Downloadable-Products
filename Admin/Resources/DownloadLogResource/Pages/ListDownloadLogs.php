<?php

declare(strict_types=1);

namespace Paymenter\Extensions\Servers\DownloadableProducts\Admin\Resources\DownloadLogResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Paymenter\Extensions\Servers\DownloadableProducts\Admin\Resources\DownloadLogResource;
use Paymenter\Extensions\Servers\DownloadableProducts\Models\DownloadLog;
use Filament\Notifications\Notification;

class ListDownloadLogs extends ListRecords
{
    protected static string $resource = DownloadLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('clear_logs')
                ->label('Clear All Logs')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function () {
                    DownloadLog::truncate();
                    
                    Notification::make()
                        ->title('Logs Cleared')
                        ->body('All download logs have been deleted.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
