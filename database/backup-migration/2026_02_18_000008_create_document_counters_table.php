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

        Schema::create('document_counters', function (Blueprint $table) {
            $table->increments('counter_id');
            $table->unsignedInteger('department_id')->unique();
            $table->unsignedInteger('section_id')->unique();
            $table->foreign('section_id')->references('section_id')->on('sections');
            $table->integer('year')->unique();
            $table->bigInteger('last_number');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_counters');
    }
};
