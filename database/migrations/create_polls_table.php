<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = config('filament-poll.table_names.polls', 'polls');

        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('multiple_choice')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('show_results_before_voting')->default(false);
            $table->boolean('allow_guest_voting')->default(false);
            $table->boolean('show_vote_count')->default(true);
            $table->timestamp('closes_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
            $table->index('closes_at');
        });
    }

    public function down(): void
    {
        $tableName = config('filament-poll.table_names.polls', 'polls');

        Schema::dropIfExists($tableName);
    }
};
