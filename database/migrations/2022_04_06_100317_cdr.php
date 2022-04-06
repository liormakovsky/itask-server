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
        Schema::create('cdr', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('customer_id');
            $table->string('did',20)->nullable();
            $table->timestamp('date_time', $precision = 0)->nullable();
            $table->unsignedInteger('num_of_calls')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string("cont_source", 2)->nullable();
            $table->string("cont_destination", 2)->nullable();
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
        Schema::dropIfExists('cdr');
    }
};
