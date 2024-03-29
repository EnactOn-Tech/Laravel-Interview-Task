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
        Schema::create('probability_rewards', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('probability_id');
            $table->decimal('awarded_percentage',8,2);
            $table->integer('awarded');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('probability_rewards');
    }
};
