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
        Schema::create('portal_settings', function (Blueprint $table) {

            $table->id();

            // Portal
            $table->string('portal_name');
            $table->text('homepage_message')->nullable();

            // Security
            $table->string('security_code');

            // Assets
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();

            // System
            $table->boolean('maintenance')->default(false);

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portal_settings');
    }
};