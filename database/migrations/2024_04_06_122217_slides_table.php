<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SlidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //


        Schema::create('slides_table', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->default('');
            $table->string('description')->default('');
            $table->string('file');
            $table->string('status')->default('pending'); // Add the status column
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('slides_table');
    }
}
