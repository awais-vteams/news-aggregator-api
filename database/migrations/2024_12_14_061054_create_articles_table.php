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
            $table->string('title', 500)->index();
            $table->string('description', 500)->index()->nullable();
            $table->string('url', 2000)->unique();
            $table->string('author', 500)->index()->nullable();
            $table->text('content')->nullable();
            $table->string('category', 500)->nullable();
            $table->string('source_name')->nullable();
            $table->string('source_url', 500)->nullable();
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
