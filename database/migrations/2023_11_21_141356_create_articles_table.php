<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('inventory_number');
            $table->string('catalog_number')->nullable();
            $table->string('draft_number')->nullable();
            $table->string('material')->nullable();
            $table->string('description');
            $table->decimal('price');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
