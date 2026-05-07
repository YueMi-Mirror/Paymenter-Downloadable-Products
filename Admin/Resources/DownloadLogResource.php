<?php

declare(strict_types=1);

namespace Paymenter\Extensions\Servers\DownloadableProducts\Admin\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Paymenter\Extensions\Servers\DownloadableProducts\Models\DownloadLog;
use Paymenter\Extensions\Servers\DownloadableProducts\Admin\Resources\DownloadLogResource\Pages\ListDownloadLogs;
use Illuminate\Database\Eloquent\Builder;

class DownloadLogResource extends Resource
{
    protected static ?string $model = DownloadLog::class;

    protected static string|\BackedEnum|null $navigationIcon = 'ri-file-paper-2-line';

    protected static string|\UnitEnum|null $navigationGroup = 'Downloadable Products';

    protected static ?string $navigationLabel = 'Download Logs';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('service.product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('file_name')
                    ->label('File Name')
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Downloaded At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort(function (Builder $query): Builder {
                return $query->orderBy('id', 'desc');
            })
            ->filters([
                //
            ])
            ->recordActions([
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDownloadLogs::route('/'),
        ];
    }
}
