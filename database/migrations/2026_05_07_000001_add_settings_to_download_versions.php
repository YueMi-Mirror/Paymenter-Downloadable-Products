<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('download_versions', function (Blueprint $table) {
            $table->string('storage_disk')->default('local')->after('file_path');
            $table->integer('download_limit')->default(0)->after('storage_disk');
            $table->integer('download_expiry')->default(0)->after('download_limit');
        });
    }

    public function down(): void
    {
        Schema::table('download_versions', function (Blueprint $table) {
            $table->dropColumn(['storage_disk', 'download_limit', 'download_expiry']);
        });
    }
};
