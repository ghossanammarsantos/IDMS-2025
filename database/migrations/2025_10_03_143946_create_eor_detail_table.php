<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEorDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eor_detail', function (Blueprint $table) {
            $table->id();
            $table->string('eor_code', 50)->nullable();
            $table->string('kode_survey', 50)->nullable();
            $table->string('no_container', 30)->nullable();

            // Detail pekerjaan
            $table->string('component', 100)->nullable();
            $table->string('location', 50)->nullable();
            $table->string('damage', 100)->nullable();
            $table->string('repair', 150)->nullable();
            $table->string('size_repair', 50)->nullable();

            // Kuantitas & jam kerja
            $table->decimal('qty', 12, 3)->nullable();
            $table->decimal('manhour', 8, 2)->nullable();
            $table->decimal('wh', 8, 2)->nullable(); // Work hours / Warehouse? (biar fleksibel)

            // Biaya
            $table->decimal('labour_cost', 14, 2)->nullable();
            $table->decimal('material_cost', 14, 2)->nullable();
            $table->decimal('total_cost', 14, 2)->nullable();

            $table->timestamps();

            // Index berguna
            $table->index(['kode_survey', 'eor_code', 'no_container'], 'idx_srvyrep_survey_eor_container');
            $table->index('component', 'idx_srvyrep_component');
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
        Schema::dropIfExists('eor_detail');
    }
}
