<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eor', function (Blueprint $table) {
            $table->id();
            $table->string('eor_code', 50)->nullable();
            $table->string('vessel', 100)->nullable();
            $table->string('voyage', 50)->nullable();
            $table->string('shipper', 150)->nullable();

            // Survey
            $table->string('pic_survey', 100)->nullable();
            $table->timestamp('estimate_date')->nullable();
            $table->timestamp('date_started')->nullable();
            $table->timestamp('date_completed')->nullable();

            // Index yang berguna
            $table->index('eor_code', 'idx_surveyin_eor_code');
            $table->index('kode_survey', 'idx_surveyin_kode_survey');
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
        Schema::dropIfExists('eor');
    }
}
