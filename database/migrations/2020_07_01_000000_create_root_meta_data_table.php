<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('root_meta_data', static function (Blueprint $table): void {
            $table->id();
            $table->uuidMorphs('metable');
            $table->string('key')->index();
            $table->json('value')->nullable();
            $table->timestamps();

            $table->unique(['metable_id', 'metable_type', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('root_meta_data');
    }
};
