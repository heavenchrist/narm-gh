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

        Schema::create('distribution_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('users','id')->nullable();
            $table->foreignId('distribution_id')->constrained()->nullable();
            $table->foreignId('user_id')->constrained()->nullable();
            $table->foreignId('region_id')->nullable()->constrained('regions')->index();
            $table->foreignId('office_id')->constrained()->nullable();
            $table->foreignId('distribution_item_id')->constrained()->nullable();
            $table->integer('quantity')->default(0);
            $table->boolean('is_received')->default(false);
            $table->dateTime('received_date')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distribution_lists');
    }
};