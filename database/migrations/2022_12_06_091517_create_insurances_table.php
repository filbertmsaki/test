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
        Schema::create('insurances', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('vehicle_id');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->unsignedInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->string('product')->nullable();
            $table->string('cover_type')->nullable();
            $table->string('usage_type')->nullable();
            $table->string('risk_note_number')->nullable();
            $table->string('debit_note_number')->nullable();
            $table->string('is_vat_exempt')->default('Y');
            $table->timestamp('receipt_date')->nullable();
            $table->string('receipt_no')->nullable();
            $table->string('receipt_reference_no')->nullable();
            $table->float('receipt_amount', 36, 2)->nullable();
            $table->string('bank_code')->nullable();
            $table->timestamp('issue_date')->nullable();
            $table->timestamp('cover_note_start_date')->nullable();
            $table->timestamp('cover_note_end_date')->nullable();
            $table->string('payment_mode')->default('BANK');
            $table->string('currency_code',3)->default('TZS');
            $table->string('status')->default('inactive');
            $table->float('sum_issued', 36, 2)->nullable();
            $table->float('premiun_excluding_vat', 36, 2)->nullable();
            $table->float('vat_percentage', 6, 2)->nullable();
            $table->float('vat_amount', 36, 2)->nullable();
            $table->float('premiun_including_vat', 36, 2)->nullable();
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
        Schema::dropIfExists('insurances');
    }
};
