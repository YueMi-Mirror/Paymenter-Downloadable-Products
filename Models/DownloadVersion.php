<?php

declare(strict_types=1);

namespace Paymenter\Extensions\Servers\DownloadableProducts\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class DownloadVersion extends Model
{
    protected $fillable = [
        'product_id',
        'version',
        'file_path',
        'storage_disk',
        'download_limit',
        'download_expiry',
        'release_notes',
        'extension_id',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
