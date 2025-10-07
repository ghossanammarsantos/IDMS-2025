<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveyInTable extends Migration
{
    public function up()
    {
        Schema::create('surveyin', function (Blueprint $table) {
            $table->id();
            $table->string('no_container', 30)->nullable();
            $table->string('jenis_container', 20)->nullable();
            $table->string('size_type', 10)->nullable();

            // Survey & status kerja
            $table->timestamp('survey_time')->nullable();
            $table->string('status_wo', 30)->nullable();
            $table->string('kegiatan1', 100)->nullable();
            $table->string('kegiatan2', 100)->nullable();
            $table->string('kegiatan', 150)->nullable();
            $table->string('kode_survey', 50)->nullable();

            // PIC & status container
            $table->string('pic', 100)->nullable();
            $table->string('status_container', 30)->nullable();
            $table->string('grade_container', 10)->nullable();

            // Gate In
            $table->string('pic_gatein', 100)->nullable();
            $table->timestamp('gatein_time')->nullable();
            $table->string('status_gatein', 30)->nullable();

            // Gate Out
            $table->string('status_gateout', 30)->nullable();
            $table->timestamp('gateout_time')->nullable();

            // Trucking & dokumen
            $table->string('no_truck', 30)->nullable();
            $table->string('driver', 100)->nullable();
            $table->string('no_bldo', 50)->nullable();

            // Lokasi penempatan di yard
            $table->string('block', 20)->nullable();
            $table->string('slot', 20)->nullable();
            $table->string('row2', 20)->nullable();
            $table->string('tier', 20)->nullable();

            // Spesifikasi/berat (pakai decimal untuk fleksibilitas satuan)
            $table->decimal('payload', 12, 3)->nullable();
            $table->decimal('tare', 12, 3)->nullable();
            $table->decimal('maxgross', 12, 3)->nullable();

            // Catatan: ada field "SIZZE" di daftar — diasumsikan typo -> tetap disimpan apa adanya
            $table->string('sizze', 10)->nullable();
            $table->timestamps();

            $table->index('no_container', 'idx_surveyin_no_container');
            $table->index('kode_survey', 'idx_surveyin_kode_survey');
            $table->index('gatein_time', 'idx_surveyin_gatein_time');
            $table->index('gateout_time', 'idx_surveyin_gateout_time');
        });
    }

    public function down()
    {
        Schema::dropIfExists('surveyin');
    }
}
