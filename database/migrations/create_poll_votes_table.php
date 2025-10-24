<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = config('filament-poll.table_names.poll_votes', 'poll_votes');
        $pollsTable = config('filament-poll.table_names.polls', 'polls');
        $pollOptionsTable = config('filament-poll.table_names.poll_options', 'poll_options');

        Schema::create($tableName, function (Blueprint $table) use ($pollsTable, $pollOptionsTable) {
            $table->id();
            $table->foreignId('poll_id')->constrained($pollsTable)->cascadeOnDelete();
            $table->foreignId('poll_option_id')->constrained($pollOptionsTable)->cascadeOnDelete();
            $table->foreignId('user_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('session_id')->nullable();
            $table->timestamps();

            $table->index(['poll_id', 'user_id']);
            $table->index(['poll_id', 'ip_address']);
            $table->index(['poll_id', 'session_id']);

            $table->unique(['poll_id', 'poll_option_id', 'user_id'], 'unique_user_vote');
            $table->unique(['poll_id', 'poll_option_id', 'session_id', 'ip_address'], 'unique_guest_vote');
        });
    }

    public function down(): void
    {
        $tableName = config('filament-poll.table_names.poll_votes', 'poll_votes');

        Schema::dropIfExists($tableName);
    }
};
