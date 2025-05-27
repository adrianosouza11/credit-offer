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
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->string('cpf',11);
            $table->decimal('simulateCredit', 10);

            $table->string('institutionName');
            $table->integer('institutionCode');
            $table->string('modalityName', 100);
            $table->string('modalityCode', 255);
            $table->decimal('modalityMonthInt', 10,4);
            $table->decimal('totalPaid', 10);
            $table->integer('instNumber');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
