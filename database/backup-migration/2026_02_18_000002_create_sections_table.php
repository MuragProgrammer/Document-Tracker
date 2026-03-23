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

        Schema::create('sections', function (Blueprint $table) {
            $table->increments('section_id');
            $table->unsignedInteger('department_id');
            $table->string('section_name', 255);
            $table->string('section_code', 255)->unique();
            $table->tinyInteger('is_active');
            $table->timestamp('created_at');
            $table->foreign('department_id')->references('department_id')->on('departments');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
