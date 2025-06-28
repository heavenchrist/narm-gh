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

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('staff_id',15)->nullable()->unique();
            $table->string('email')->unique();
            $table->date('date_of_birth')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->string('residential_address')->nullable();
            $table->string('telephone')->nullable()->unique();
            $table->string('pin_number')->nullable()->unique();
            $table->string('registration_number')->nullable()->unique();
            $table->string('place_of_work')->nullable();
			$table->foreignId('rank_id')->index()->nullable();
			$table->foreignId('region_id')->index()->nullable();
            $table->string('district')->nullable();
            $table->string('image_url')->nullable()->unique();
            $table->string('gender')->nullable()->index();
            $table->string('marital_status')->nullable()->index();
            $table->string('next_of_kin')->nullable()->index();
            $table->string('next_of_kin_contact')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('token')->nullable()->unique();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};