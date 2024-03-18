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
        Schema::create('return_balance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_order_id')->nullable();
            $table->date('date');
            $table->decimal('amount', 10, 2);
            $table->text('description');
            $table->string('payment_mode');
            $table->string('payment_details');
            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            $table->foreign('job_order_id')->references('id')->on('tenders')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_balance');
    }
};
