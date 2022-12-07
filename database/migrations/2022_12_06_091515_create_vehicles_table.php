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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('RegistrationNumber');
            $table->string('BodyType');
            $table->string('SittingCapacity');
            $table->string('MotorCategory');
            $table->string('ChassisNumber');
            $table->string('Make');
            $table->string('Model');
            $table->string('ModelNumber');
            $table->string('Color');
            $table->string('EngineNumber');
            $table->string('EngineCapacity');
            $table->string('FuelUsed');
            $table->string('NumberOfAxles');
            $table->string('AxleDistance');
            $table->string('YearOfManufacture');
            $table->string('TareWeight');
            $table->string('GrossWeight');
            $table->string('MotorUsage');
            $table->string('OwnerName');
            $table->string('OwnerCategory');
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
        Schema::dropIfExists('vehicles');
    }
};
