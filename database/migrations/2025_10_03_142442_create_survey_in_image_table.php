<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveyInImageTable extends Migration
{
    public function up()
    {
        Schema::create('survey_in_image', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kode_survey');
            $table->unsignedBigInteger('syarat_id');
            $table->string('image_path', 255);
            $table->string('description', 255)->nullable();
            $table->foreign('kode_survey')->references('id')->on('surveyin');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('survey_in_image');
    }
}
