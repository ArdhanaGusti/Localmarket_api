<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('order');
            $table->foreignId('user_id');
            $table->foreignId('product_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
