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
        Schema::create('invoice_purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_order_id')->nullable();
            $table->string('invoice_no')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->string('type');
            $table->date('date');
            $table->decimal('final_total', 10, 2);

            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();

            $table->foreign('job_order_id')->references('id')->on('tenders')->onDelete('set null');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_purchases');
    }
};
