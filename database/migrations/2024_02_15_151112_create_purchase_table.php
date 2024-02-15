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
        Schema::create('purchase', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_order_id')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('type');
            $table->date('date');
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->unsignedBigInteger('material_id')->nullable();
            $table->integer('quantity');
            $table->string('unit');
            $table->decimal('amount', 10, 2);
            $table->decimal('gst', 10, 2);
            $table->decimal('total', 10, 2);

            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();

            $table->foreign('job_order_id')->references('id')->on('tenders')->onDelete('set null');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase');
    }
};
