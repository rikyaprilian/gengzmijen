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
        Schema::create('links', function (Blueprint $table) {

            $table->id();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('title');

            $table->string('description')->nullable();

            $table->text('url');

            $table->string('icon')->default('link-45deg');

            $table->string('color')->default('blue');

            $table->integer('sort_order')->default(0);

            $table->boolean('is_active')->default(true);

            $table->boolean('open_new_tab')->default(true);

            $table->unsignedBigInteger('click_count')->default(0);

            $table->timestamps();
            
            $table->string('slug')->nullable()->unique();

            $table->string('badge')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
