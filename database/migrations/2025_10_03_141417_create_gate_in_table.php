<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGateInTable extends Migration
{
    public function up()
    {
        Schema::create('gate_in', function (Blueprint $table) {
            $table->id();
            $table->string('no_container', 30);
            $table->string('jenis_container', 20)->nullable();
            $table->string('size_type', 10)->nullable();
            $table->timestamp('gatein_time')->nullable();
            $table->string('pic_gatein', 100)->nullable();
            $table->string('no_bldo', 50)->nullable();
            $table->index('no_container', 'idx_cg_no_container');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gate_in');
    }
}
