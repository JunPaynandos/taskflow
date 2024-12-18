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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['not started', 'in progress', 'completed']);
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->date('due_date')->nullable();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade'); // Ensure this is correctly defined
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
