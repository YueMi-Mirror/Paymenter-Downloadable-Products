<?php

declare(strict_types=1);

namespace Paymenter\Extensions\Servers\DownloadableProducts\Admin\Resources;

use App\Models\Product;
use App\Models\Server;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Paymenter\Extensions\Servers\DownloadableProducts\Models\DownloadVersion;
use Paymenter\Extensions\Servers\DownloadableProducts\Admin\Resources\DownloadVersionResource\Pages\CreateDownloadVersion;
use Paymenter\Extensions\Servers\DownloadableProducts\Admin\Resources\DownloadVersionResource\Pages\EditDownloadVersion;
use Paymenter\Extensions\Servers\DownloadableProducts\Admin\Resources\DownloadVersionResource\Pages\ListDownloadVersions;

class DownloadVersionResource extends Resource
{
    protected static ?string $model = DownloadVersion::class;

    protected static string|\BackedEnum|null $navigationIcon = 'ri-file-list-3-line';

    protected static string|\UnitEnum|null $navigationGroup = 'Downloadable Products';

    protected static ?string $navigationLabel = 'Versions';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Version Details')
                    ->schema([
                        Select::make('product_id')
                            ->label('Product')
                            ->options(function () {
                                $server = Server::where('extension', 'DownloadableProducts')->first();
                                if (!$server) {
                                    return [];
                                }
                                return Product::where('server_id', $server->id)->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required(),
                        TextInput::make('version')
                            ->label('Version Number')
                            ->placeholder('e.g. 1.0.0, v2.1-beta')
                            ->required(),
                        Select::make('storage_disk')
                            ->label('Storage Disk')
                            ->options([
                                'local' => 'Local',
                                's3' => 'S3',
                                'public' => 'Public',
                            ])
                            ->default('local')
                            ->required(),
                        FileUpload::make('file_path')
                            ->label('Downloadable File')
                            ->disk(fn (Get $get) => $get('storage_disk') ?? 'local')
                            ->directory('DownloadableProducts/versions')
                            ->preserveFilenames()
                            ->required(),
                        Grid::make()
                            ->columns(2)
                            ->schema([
                                TextInput::make('download_limit')
                                    ->label('Download Limit')
                                    ->numeric()
                                    ->default(0)
                                    ->hint('0 for unlimited'),
                                TextInput::make('download_expiry')
                                    ->label('Download Expiry (days)')
                                    ->numeric()
                                    ->default(0)
                                    ->hint('0 for no expiry'),
                            ]),
                        Textarea::make('release_notes')
                            ->label('Release Notes')
                            ->rows(5),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('version')
                    ->label('Version')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Release Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('product')
                    ->label('Product')
                    ->relationship('product', 'name'),
            ])
            ->recordActions([
                EditAction::make(),
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
            'index' => ListDownloadVersions::route('/'),
            'create' => CreateDownloadVersion::route('/create'),
            'edit' => EditDownloadVersion::route('/{record}/edit'),
        ];
    }
}
