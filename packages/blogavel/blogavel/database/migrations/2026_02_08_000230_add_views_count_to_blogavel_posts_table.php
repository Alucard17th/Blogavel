<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blogavel_posts', function (Blueprint $table): void {
            $table->unsignedBigInteger('views_count')->default(0)->after('author_id');
        });
    }

    public function down(): void
    {
        Schema::table('blogavel_posts', function (Blueprint $table): void {
            $table->dropColumn('views_count');
        });
    }
};
