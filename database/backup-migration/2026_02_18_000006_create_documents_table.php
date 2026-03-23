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

        Schema::create('documents', function (Blueprint $table) {
            $table->increments('doc_id');
            $table->string('document_number', 255);
            $table->unsignedInteger('type_id');
            $table->foreign('type_id')->references('type_id')->on('document_types');
            $table->string('document_name', 255);
            $table->unsignedInteger('originating_section_id');
            $table->foreign('originating_section_id')->references('section_id')->on('sections');
            $table->integer('created_by');
            $table->unsignedInteger('current_section_id');
            $table->foreign('current_section_id')->references('section_id')->on('sections');
            $table->unsignedInteger('current_holder_id')->nullable();
            $table->foreign('current_holder_id')->references('user_id')->on('users')->onDelete('set null');
            $table->enum('status', ['CREATED', 'PENDING', 'UNDER REVIEW', 'END OF CYCLE', 'REOPENED']);
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
        Schema::dropIfExists('documents');
    }
};
