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
        Schema::table('users', function (Blueprint $table) {
            // Agregar solo la columna employee_id como opcional
            $table->unsignedBigInteger('employee_id')->nullable()->after('password');

            // Agregar relación
            $table->foreign('employee_id')->references('id')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminar clave foránea
            $table->dropForeign(['employee_id']);

            // Eliminar columna
            $table->dropColumn('employee_id');
        });
    }
};
