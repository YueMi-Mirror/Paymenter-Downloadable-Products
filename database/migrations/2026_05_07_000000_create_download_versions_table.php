<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('download_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('version');
            $table->string('file_path');
            $table->text('release_notes')->nullable();
            $table->string('extension_id')->default('DownloadableProducts');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('download_versions');
    }
};
