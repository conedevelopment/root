<?php

declare(strict_types=1);

use Cone\Root\Models\User;
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
        Schema::create('root_events', static function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(User::getProxiedClass())->nullable()->constrained()->nullOnDelete();
            $table->uuidMorphs('target');
            $table->string('action');
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('root_events');
    }
};
