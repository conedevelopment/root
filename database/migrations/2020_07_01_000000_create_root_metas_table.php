<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('root_metas', static function (Blueprint $table): void {
            $table->id();
            $table->uuidMorphs('metable');
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['metable_id', 'metable_type', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('root_metas');
    }
};
