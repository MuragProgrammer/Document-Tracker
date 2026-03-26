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
            $table->increments('user_id');
            $table->string('first_name', 255);
            $table->string('middle_name', 255);
            $table->string('last_name', 255);
            $table->string('username', 255);
            $table->string('password_hash', 255);
            $table->unsignedInteger('section_id');
            $table->foreign('section_id')->references('section_id')->on('sections');
            $table->unsignedInteger('position_id');
            $table->foreign('position_id')->references('position_id')->on('positions');
            $table->enum('role', ['ADMIN', 'EMPLOYEE']);
            $table->tinyInteger('is_active');
            $table->timestamps();
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
