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

        Schema::create('training_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained('users');
            $table->foreignId('training_id')->nullable()->constrained();
            $table->boolean('attended')->default(false);
            $table->foreignId('user_id')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_registrations');
    }
};
