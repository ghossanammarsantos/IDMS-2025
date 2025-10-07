<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnImportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ann_import', function (Blueprint $table) {
            $table->id();
            $table->string('no_container', 30)->nullable();
            $table->string('jenis_container', 20)->nullable();
            $table->string('size_type', 10)->nullable();

            // Status & waktu proses
            $table->timestamp('set_time')->nullable();
            $table->string('status_survey', 30)->nullable();
            $table->string('status_gate', 30)->nullable();
            $table->timestamp('in_time')->nullable();
            $table->timestamp('out_time')->nullable();
            $table->timestamp('survey_time')->nullable();

            // Customer & shipment
            $table->string('consignee', 150)->nullable();
            $table->string('customer_code', 50)->nullable();
            $table->string('ex_vessel', 100)->nullable();
            $table->string('voyage', 50)->nullable();
            $table->string('no_bldo', 50)->nullable();

            // Lain-lain
            $table->timestamp('tanggal_berthing')->nullable();
            $table->text('remarks')->nullable();

            // Laravel timestamps
            $table->timestamps();

            // Index berguna
            $table->index('no_container', 'idx_svh_no_container');
            $table->index('customer_code', 'idx_svh_customer_code');
            $table->index('survey_time', 'idx_svh_survey_time');
            $table->index('in_time', 'idx_svh_in_time');
            $table->index('out_time', 'idx_svh_out_time');
            $table->index(['ex_vessel', 'voyage'], 'idx_svh_vessel_voyage');
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
        Schema::dropIfExists('ann_import');
    }
}
