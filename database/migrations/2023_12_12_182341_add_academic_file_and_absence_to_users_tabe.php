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
        Schema::table('excuses', function (Blueprint $table) {
            $table->string('academic_file')->nullable(); // Add this line
            $table->string('absence')->nullable(); // Add this line
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('excuses', function (Blueprint $table) {
            $table->dropColumn('academic_file'); // Add this line
            $table->dropColumn('absence'); // Add this line
        });
        
    }
};
