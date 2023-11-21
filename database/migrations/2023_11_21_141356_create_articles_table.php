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
            $table->string('inventory_number');
            $table->string('catalog_number');
            $table->string('draft_number');
            $table->string('material_number');
            $table->string('description');
            $table->decimal('price');
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
