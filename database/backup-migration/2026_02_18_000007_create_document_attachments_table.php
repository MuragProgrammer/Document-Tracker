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

        Schema::create('document_attachments', function (Blueprint $table) {
            $table->increments('attachment_id');
            $table->unsignedInteger('doc_id');
            $table->string('file_original_name');
            $table->string('file_stored_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->integer('file_size');
            $table->integer('version_number');
            $table->tinyInteger('is_active');
            $table->integer('uploaded_by');
            $table->dateTime('uploaded_at');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_attachments');
    }
};
