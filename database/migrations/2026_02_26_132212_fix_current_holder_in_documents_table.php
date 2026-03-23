<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // 1️⃣ Drop the wrong foreign key first
            $table->dropForeign(['current_holder_id']);

            // 2️⃣ Make the column nullable (to avoid issues if some rows don't have a user)
            $table->unsignedInteger('current_holder_id')->nullable()->change();

            // 3️⃣ Add correct foreign key to users table
            $table->foreign('current_holder_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Drop the correct FK
            $table->dropForeign(['current_holder_id']);

            // Optionally, revert to old FK (not recommended if sections no longer make sense)
            $table->foreign('current_holder_id')
                  ->references('section_id')
                  ->on('sections');
        });
    }
};
