<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            // Foreign key UUID ke projects.id
            $table->uuid('project_id')->nullable();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');

            // Foreign key ke users (penerima tugas)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Foreign key ke users (yang ngasih tugas)
            $table->foreignId('assigned_by')->constrained('users')->onDelete('cascade');

            // Info tugas
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['To Do', 'In Progress', 'Done'])->default('To Do');
            $table->enum('priority', ['Low', 'Medium', 'High'])->default('Medium');
            $table->date('deadline')->nullable();
            $table->string('tags')->nullable(); // JSON atau array
            $table->integer('estimated_time')->nullable(); // dalam jam
            $table->string('attachment_path')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
