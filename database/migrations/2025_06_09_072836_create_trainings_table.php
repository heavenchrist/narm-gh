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
        Schema::disableForeignKeyConstraints();

        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->dateTime('registration_end_date');
            $table->dateTime('start');
            $table->dateTime('end');
            $table->string('training_mode');
            $table->boolean('status')->default(false);
            $table->text('content');
            $table->foreignId('user_id')->constrained();
            $table->foreignId('region_id')->nullable()->constrained();
            $table->unique(['name', 'region_id']);
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
