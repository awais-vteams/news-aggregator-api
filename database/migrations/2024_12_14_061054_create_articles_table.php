<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->ulid()->primary();
            $table->string('title')->index();
            $table->string('description')->index();
            $table->string('url')->unique();
            $table->string('author')->index();
            $table->text('content');
            $table->string('category');
            $table->string('source_name')->index();
            $table->string('source_url');
            $table->datetime('published_at')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
