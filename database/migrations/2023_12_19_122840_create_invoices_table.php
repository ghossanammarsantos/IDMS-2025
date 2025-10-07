<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    /*{
        "ad_org_id": 1000001,
        "dateinvoiced": "2023-11-27",
        "dateacct": "2023-11-27",
        "description_header": "Tes Alif",
        "poreference": "TES ALIF",
        "paymentrule": "P",
        "c_paymentterm_id": 1000003,
        "c_doctypetarget_id": 1000005,
        "c_bpartner_id": 1000003,
        "m_pricelist_id": 1000001,
        "issotrx": "N",
        "c_activity_id": 0,
        "line_list": [
          {
            "m_product_id": 1000008,
            "c_uom_id": 1000001,
            "description_line": "TES ALIF",
            "c_tax_id": 1000001,
            "qty": 1,
            "priceentered": 500000
          }
        ]
      }
      */
    public function up()
    {
        Schema::create('INVOICES', function (Blueprint $table) {
            $table->id();
            $table->integer('ad_org_id');
            $table->timestamps('dateinvoiced');
            $table->timestamps('dateacct');
            $table->string('description_header');
            $table->string('poreference');
            $table->string('paymentrule');
            $table->integer('c_paymentterm_id');
            $table->integer('c_doctypetarget_id');
            $table->integer('c_bpartner_id');
            $table->string('m_pricelist_id');
            $table->string('issotrx');
            $table->integer('c_activity_id');
            $table->string('line_list');
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
        Schema::dropIfExists('invoices');
    }
}
