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
        Schema::create('contributions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('staff_id')->index();
            $table->string('place_of_work');
            $table->string('district');
            $table->string('region');
            $table->string('period');
            $table->integer('amount');
            $table->foreignId('user_id')->nullable();
            $table->timestamps();
            $table->unique(["staff_id", "period"], 'staff_period_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('contributions');
    }
};