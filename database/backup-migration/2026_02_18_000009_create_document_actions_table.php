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
        Schema::create('document_actions', function (Blueprint $table) {
            $table->increments('action_id');

            $table->unsignedInteger('doc_id');
            $table->unsignedInteger('section_id');
            $table->unsignedInteger('user_id');

            $table->enum('action_type', ['CREATED', 'FORWARDED', 'RECEIVED', 'END OF CYCLE', 'REOPEN']);
            $table->text('remarks');
            $table->dateTime('action_datetime');

            // Foreign keys
            $table->foreign('doc_id')->references('doc_id')->on('documents')->onDelete('cascade');
            $table->foreign('section_id')->references('section_id')->on('sections');
            $table->foreign('user_id')->references('user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_actions');
    }
};
