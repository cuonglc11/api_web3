<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('watllets', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->string('address_wallet');
            $table->string('private_key');
            $table->decimal('blance' , 8,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watllets');
    }
};
