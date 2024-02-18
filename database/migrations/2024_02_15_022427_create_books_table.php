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
        Schema::create('books', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('type_id');
            $table->foreign('type_id')->references('id')->on('types');

            $table->integer('total_loan')->default(0);
            $table->integer('total_book')->default(0);

            $table->string('title');
            $table->string('slug');
            $table->string('writer');
            $table->string('publisher')->nullable();
            $table->text('description');
            $table->integer('year')->nullable();
            $table->integer('page');
            $table->string('cover')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
