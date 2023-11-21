<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');
            $table->string('position');
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
