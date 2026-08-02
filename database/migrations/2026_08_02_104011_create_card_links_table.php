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
        Schema::create('card_links', function (Blueprint $table) {
            $table->id();

            $table->foreignId('card_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->uuid('uuid')->unique();

            $table->string('title');

            $table->text('url');

            $table->string('icon')->nullable();

            $table->unsignedInteger('sort_order')->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card_links');
    }
};
