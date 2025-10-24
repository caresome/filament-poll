<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = config('filament-poll.table_names.poll_options', 'poll_options');
        $pollsTable = config('filament-poll.table_names.polls', 'polls');

        Schema::create($tableName, function (Blueprint $table) use ($pollsTable) {
            $table->id();
            $table->foreignId('poll_id')->constrained($pollsTable)->cascadeOnDelete();
            $table->string('text');
            $table->integer('votes_count')->default(0);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['poll_id', 'order']);
        });
    }

    public function down(): void
    {
        $tableName = config('filament-poll.table_names.poll_options', 'poll_options');

        Schema::dropIfExists($tableName);
    }
};
