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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('dni')->unique()->unsigned();
            $table->string('last_name', 100);
            $table->string('middle_name', 100);
            $table->string('first_names', 100);
            $table->foreignId('department_id')
                ->constrained()
                ->onDelete('cascade');
            $table->timestamps();
        });

        DB::statement('ALTER TABLE employees ADD CONSTRAINT check_dni_range CHECK (dni >= 10000000 AND dni <= 99999999)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
