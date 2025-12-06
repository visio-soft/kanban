<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('issues')) {
            Schema::create('issues', function (Blueprint $table) {
                $table->id();
                $table->foreignId('board_id')->constrained('boards')->cascadeOnDelete();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('status')->default('backlog');
                $table->integer('order')->default(0);
                $table->string('priority')->default('medium');
                $table->date('due_date')->nullable();
                $table->timestamp('start_at')->nullable();
                $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
                $table->json('tags')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
