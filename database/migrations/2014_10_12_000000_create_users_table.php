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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('role')->default('user');
            $table->string('position')->default('Member');

            $table->string('verified')->default('Not Verified');

            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('proPic')->nullable();

            $table->string('username');
            $table->string('password');

            $table->string('slug')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
