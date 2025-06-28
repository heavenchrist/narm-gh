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

        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('report_url')->unique()->index();
            $table->foreignId('user_id')->constrained()->nullable();
            $table->foreignId('office_id')->constrained()->nullable();
            $table->foreignId('region_id')->constrained()->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users', 'id');
            $table->boolean('is_submitted')->default(false);
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
        Schema::dropIfExists('reports');
    }
};