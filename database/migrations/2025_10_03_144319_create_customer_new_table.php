<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerNewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_new', function (Blueprint $table) {
            $table->id();
            $table->string('kode_customer', 50)->nullable();   // jadikan unique jika perlu
            $table->string('nama_customer', 150)->nullable();
            $table->string('alamat', 255)->nullable();
            $table->string('kota', 100)->nullable();
            $table->string('negara', 100)->nullable();
            $table->string('kategori_customer', 50)->nullable();

            // Tanggal bergabung & set time
            $table->date('tgl_bergabung')->nullable();        // hanya tanggal
            $table->timestamp('set_time')->nullable();        // jika butuh jam-menit-detik

            // Laravel timestamps
            $table->timestamps();

            // Index berguna
            $table->index('kode_customer', 'idx_cust_kode');
            $table->index('kota', 'idx_cust_kota');
            $table->index('negara', 'idx_cust_negara');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_new');
    }
}
