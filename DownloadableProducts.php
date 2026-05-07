<?php

namespace Paymenter\Extensions\Servers\DownloadableProducts;

use App\Classes\Extension\Server;
use App\Models\Service;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Paymenter\Extensions\Servers\DownloadableProducts\Models\DownloadLog;
use Paymenter\Extensions\Servers\DownloadableProducts\Models\DownloadVersion;

class DownloadableProducts extends Server
{
    #[ExtensionMeta(
        name: 'Downloadable Products',
        description: 'A free to use extension to sell downloadable files with optimal security',
        version: '1.3',
        author: 'QKing',
        url: 'https://host4you.cloud',
        icon: 'https://paymenter.org/logo-dark.svg'
    )]
    public function boot()
    {
        require __DIR__ . '/routes.php';
        View::addNamespace('downloadableproducts', __DIR__ . '/resources/views');
    }

    public function installed()
    {
        ExtensionHelper::runMigrations('extensions/Servers/DownloadableProducts/database/migrations');
    }

    public function getConfig($values = []): array
    {
        return [
            [
                'name' => 'Notice',
                'type' => 'placeholder',
                'label' => 'You can use this extension to manage downloadable products.',
            ],
        ];
    }

    public function getProductConfig($values = []): array
    {
        return [
            [
                'name' => 'Notice',
                'type' => 'placeholder',
                'label' => 'Files and versions are managed in the "Versions" admin area.',
            ],
        ];
    }

    private function getDisk(array $settings): \Illuminate\Contracts\Filesystem\Filesystem
    {
        $disk = $settings['storage_disk'] ?? 'local';

        return Storage::disk($disk);
    }

    public function createServer(Service $service, $settings, $properties)
    {
        return true;
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function getActions(Service $service, $settings, $properties): array
    {
        return [
            ['type' => 'button', 'label' => 'Download', 'function' => 'download'],
            ['type' => 'view', 'name' => 'Download', 'label' => 'Download File'],
        ];
    }

    public function getView(Service $service, $settings, $properties, $view)
    {
        $settingsArray = is_object($settings) ? (array) $settings : $settings;

        if (!empty($settingsArray['file_upload'])) {
            $filePath = $settingsArray['file_upload'];
            $disk = $this->getDisk($settingsArray);
            if ($disk->exists($filePath)) {
                $settingsArray['file_checksum'] = hash('sha256', $disk->get($filePath));
            }
        }

        $versions = DownloadVersion::where('product_id', $service->product_id)->latest()->get();

        return view('downloadableproducts::overview', [
            'service' => $service,
            'settings' => $settingsArray,
            'versions' => $versions,
        ]);
    }

    public function download(Service $service, $settings = null, $properties = null, $versionId = null)
    {
        if ($settings === null || is_string($settings) || is_numeric($settings)) {
            if (is_string($settings) || is_numeric($settings)) {
                $versionId = $settings;
            }
            $settings = $service->product?->settings ?? [];
            if (is_object($settings)) {
                $settings = (array) $settings;
            }
        }

        $version = null;
        if ($versionId) {
            $version = DownloadVersion::where('product_id', $service->product_id)->find($versionId);
        } else {
            $version = DownloadVersion::where('product_id', $service->product_id)->latest()->first();
        }

        if ($version) {
            $fileUpload = $version->file_path;
            $downloadLimit = (int) $version->download_limit;
            $expiryDays = (int) $version->download_expiry;
            $diskName = $version->storage_disk;
        } else {
            $fileUpload = $settings['file_upload'] ?? null;
            $downloadLimit = (int) ($settings['download_limit'] ?? 0);
            $expiryDays = (int) ($settings['download_expiry'] ?? 0);
            $diskName = $settings['storage_disk'] ?? 'local';
        }

        if (!$fileUpload) {
            session()->flash('error', 'File not found for this product.');

            return redirect()->back();
        }

        if ($expiryDays > 0) {
            $expiryDate = $service->created_at->addDays($expiryDays);
            if (now()->greaterThan($expiryDate)) {
                session()->flash('error', 'Download period has expired.');

                return redirect()->back();
            }
        }

        if ($downloadLimit > 0 && $service->download_count >= $downloadLimit) {
            session()->flash('error', 'Download limit reached.');

            return redirect()->back();
        }

        $disk = Storage::disk($diskName);

        if (!$disk->exists($fileUpload)) {
            session()->flash('error', 'File not found on storage.');

            return redirect()->back();
        }

        $service->increment('download_count');

        DownloadLog::create([
            'service_id' => $service->id,
            'file_name' => basename($fileUpload),
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
        ]);

        return $disk->download($fileUpload, basename($fileUpload), [
            'Content-Type' => 'application/octet-stream',
        ]);
    }
}
