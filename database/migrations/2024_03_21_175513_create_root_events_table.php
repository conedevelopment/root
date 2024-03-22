<?php

use Cone\Root\Models\User;
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
        Schema::create('root_events', static function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(User::getProxiedClass())->nullable()->constrained()->nullOnDelete();
            $table->morphs('target');
            $table->string('action');
            $table->string('label');
            $table->json('payload')->nullable();
            $table->timestamp('created_at');
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
