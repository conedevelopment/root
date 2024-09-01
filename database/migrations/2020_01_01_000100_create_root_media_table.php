<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('root_media', static function (Blueprint $table): void {
            $table->id();
            $table->uuid();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
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
            $table->foreignId('medium_id')->constrained('root_media')->cascadeOnDelete();
            $table->uuidMorphs('mediable');
            $table->string('collection')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('root_mediables');
        Schema::dropIfExists('root_media');
    }
};
