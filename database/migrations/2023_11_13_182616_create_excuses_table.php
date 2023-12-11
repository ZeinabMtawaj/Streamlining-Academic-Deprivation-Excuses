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
        Schema::create('excuses', function (Blueprint $table) {
            $table->id();
            $table->string('excuse_file_path'); // Storing file path
            $table->foreignId('deprivation_id')->constrained('deprivations')->onDelete('cascade');
            $table->enum('advisor_decision', ['Pending', 'Approved', 'Rejected']);
            $table->enum('committee_decision', ['Pending', 'Approved', 'Rejected']);
            $table->enum('final_decision', ['Pending', 'Approved', 'Rejected']);
            $table->string('rejection_reason_file_path')->nullable(); // Storing file path
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('excuses');
    }
};
