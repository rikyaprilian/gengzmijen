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
        Schema::table('cards', function (Blueprint $table) {
            if (! Schema::hasColumn('cards', 'color')) {
                $table->string('color')->nullable()->after('badge');
            }
        });

        Schema::table('card_links', function (Blueprint $table) {
            if (! Schema::hasColumn('card_links', 'color')) {
                $table->string('color')->nullable()->after('icon');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            if (Schema::hasColumn('cards', 'color')) {
                $table->dropColumn('color');
            }
        });

        Schema::table('card_links', function (Blueprint $table) {
            if (Schema::hasColumn('card_links', 'color')) {
                $table->dropColumn('color');
            }
        });
    }
};
