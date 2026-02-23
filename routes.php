<?php

use Illuminate\Support\Facades\Route;
use Paymenter\Extensions\Servers\DownloadableProducts\DownloadableProducts;

Route::get('/downloadable-products/download/{service}', [DownloadableProducts::class, 'download'])
    ->name('service.download');
