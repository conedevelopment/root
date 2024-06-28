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
        Schema::table('users', function (Blueprint $table) {
            $table->after('id', function (Blueprint $table) {
                $table->nullableMorphs('employer');
            });
            $table->after('password', function (Blueprint $table) {
                $table->json('settings')->nullable();
            });
            $table->after('updated_at', function (Blueprint $table) {
                $table->softDeletes();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropMorphs('employer');
            $table->dropColumn('settings');
            $table->dropSoftDeletes();
        });
    }
};
