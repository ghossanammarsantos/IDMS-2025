<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveyInSyaratImageTable extends Migration
{
    public function up()
    {
        Schema::create('survey_in_syarat_image', function (Blueprint $table) {
            $table->id();
            $table->string('nama_syarat', 255);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('survey_in_syarat_image');
    }
}
