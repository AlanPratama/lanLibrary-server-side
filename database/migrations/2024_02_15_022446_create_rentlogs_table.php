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
        Schema::create('rentlogs', function (Blueprint $table) {
            $table->id();

            $table->string('code');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedBigInteger('book_id');
            $table->foreign('book_id')->references('id')->on('books');


            $table->date('date_start');
            $table->date('date_finish');
            $table->date('return')->nullable();

            $table->integer('day_late')->nullable();
            $table->integer('penalties')->nullable();



            $table->string('status')->default('Need Verification');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentlogs');
    }
};
