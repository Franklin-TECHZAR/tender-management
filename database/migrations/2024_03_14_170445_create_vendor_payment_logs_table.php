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
        Schema::create('vendor_payment_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vendor_payment_id')->unsigned();
            $table->date('date');
            $table->float('amount', 10, 2);
            $table->string('type');
            $table->text('description');
            $table->string('payment_for');
            $table->string('payment_mode');
            $table->text('payment_details');

            $table->foreign('vendor_payment_id')->references('id')->on('vendor_payments')->onDelete('cascade');

            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_payment_logs');
    }
};
