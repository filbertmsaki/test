<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('customer_name');
            $table->date('customer_date_of_birth')->nullable();
            $table->string('customer_id_type')->nullable();
            $table->string('customer_id_number')->nullable();
            $table->string('customer_tin')->nullable();
            $table->string('customer_vrn')->nullable();
            $table->string('customer_gender')->nullable();
            $table->string('customer_country')->default('TZA');
            $table->string('customer_region')->nullable();
            $table->string('customer_district')->nullable();
            $table->string('customer_phone_number');
            $table->string('customer_address');
            $table->string('customer_email_address')->nullable();
            $table->string('customer_type')->nullable();
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
        Schema::dropIfExists('customers');
    }
};
