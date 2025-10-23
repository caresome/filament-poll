<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('poll_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->cascadeOnDelete();
            $table->string('text');
            $table->integer('votes_count')->default(0);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['poll_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poll_options');
    }
};
