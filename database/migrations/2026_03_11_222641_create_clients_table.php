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
     Schema::create('clients', function (Blueprint $table) {
        $table->id();
        
        // Pamata dati
        $table->string('name'); // Vārds vai Uzņēmuma nosaukums
        $table->string('email')->unique(); // Unikāls e-pasts (drošība pret dublikātiem)
        $table->string('phone')->nullable(); // Tālrunis (nav obligāts)
        $table->string('company')->nullable(); // Uzņēmuma nosaukums (nav obligāts)
        $table->text('notes')->nullable(); // Papildu piezīmes par klientu
        
        // Statuss (pagaidām kā string, jo Enums kontrolēsim modeļa līmenī)
        $table->string('status')->default('active'); 
        
        // Drošība un Auditācija
        $table->softDeletes(); // Ļauj atjaunot nejauši izdzēstus klientus
        $table->timestamps(); // create_at un updated_at kolonnas
      });
   }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
