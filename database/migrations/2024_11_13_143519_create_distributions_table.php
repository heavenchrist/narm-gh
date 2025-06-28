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

        Schema::create('distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribution_item_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('office_id')->constrained();
            $table->integer('quantity')->default(0);
            $table->boolean('is_received')->default(false);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributions');
    }
};