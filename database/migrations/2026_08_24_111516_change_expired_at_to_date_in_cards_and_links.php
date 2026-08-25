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
        // cards: ubah expired_at dari timestamp ke date
        Schema::table('cards', function (Blueprint $table) {
            $table->date('expired_at')->nullable()->change();
        });

        // card_links: tambah expired_at (date) jika belum ada, atau ubah tipenya
        if (Schema::hasColumn('card_links', 'expired_at')) {
            Schema::table('card_links', function (Blueprint $table) {
                $table->date('expired_at')->nullable()->change();
            });
        } else {
            Schema::table('card_links', function (Blueprint $table) {
                $table->date('expired_at')->nullable()->after('is_active');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cards_and_links', function (Blueprint $table) {
            //
        });
    }
};
