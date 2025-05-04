<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_status', function (Blueprint $table) {
            $table->id();
            $table->float('code');
            $table->string('name');
            $table->binary('icon')->nullable(); // สร้างแบบ BLOB ก่อน
            $table->timestamps();
        });

        DB::statement('ALTER TABLE user_status MODIFY icon LONGBLOB NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_users');
    }
};
