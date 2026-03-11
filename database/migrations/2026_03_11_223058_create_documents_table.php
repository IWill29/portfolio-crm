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
        Schema::create('documents', function (Blueprint $table) {
        $table->id();
        
        // Piesaistām dokumentu konkrētam projektam
        $table->foreignId('project_id')->constrained()->cascadeOnDelete();
        
        $table->string('title'); // Dokumenta nosaukums (piem., "Gala līgums")
        
        // Lietotāja definēts tips (piem., "Līgums", "Rēķins")
        $table->string('type')->nullable(); 
        
        $table->text('notes')->nullable(); // Papildu piezīmes par failu
        
        $table->softDeletes(); // Standarts drošai datu pārvaldībai
        $table->timestamps();

       });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
