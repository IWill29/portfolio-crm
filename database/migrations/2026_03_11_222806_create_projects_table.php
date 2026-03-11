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
        Schema::create('projects', function (Blueprint $table) {
        $table->id();
        
        // Relācija ar klientu tabulu
        // cascadeOnDelete() nodrošina, ka izdzēšot klientu, pazūd arī viņa projekti (datu integritāte)
        $table->foreignId('client_id')->constrained()->cascadeOnDelete();
        
        $table->string('title');
        $table->text('description')->nullable();
        
        // Budžetam vienmēr izmanto decimal (precizitātei), nevis float
        $table->decimal('budget', 12, 2)->nullable();
        
        // Šeit glabāsim Enum vērtības kā string (piem., 'idea', 'in_progress')
        // Modeļa līmenī Casts parūpēsies par to pārvēršanu PHP objektos
        $table->string('status')->default('idea'); 
        $table->string('priority')->default('medium');
        
        $table->date('starts_at')->nullable();
        $table->date('ends_at')->nullable();
        
        $table->softDeletes(); // Drošības standarts
        $table->timestamps();
       });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
