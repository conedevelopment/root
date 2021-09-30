<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('root_media', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name')->index();
            $table->string('file_name');
            $table->string('mime_type');
            $table->unsignedInteger('size');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('disk');
            $table->json('properties')->nullable();
            $table->timestamps();
        });

        Schema::create('root_mediables', static function (Blueprint $table): void {
            $table->id();
            $table->foreignUuid('medium_id')->constrained('root_media')->cascadeOnDelete();
            $table->morphs('mediable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('root_mediables');
        Schema::dropIfExists('root_media');
    }
};
