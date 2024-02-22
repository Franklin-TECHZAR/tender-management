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
        Schema::create('invoice_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_purchase_id');
            $table->unsignedBigInteger('material_id')->nullable();
            $table->unsignedInteger('quantity');
            $table->string('unit');
            $table->decimal('amount', 10, 2);
            $table->decimal('gst', 10, 2);
            $table->decimal('total', 10, 2);
            $table->dateTime('deleted_at')->nullable();

            $table->timestamps();

            $table->foreign('invoice_purchase_id')->references('id')->on('invoice_purchases')->onDelete('cascade');
			$table->foreign('material_id')->references('id')->on('materials')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_products');
    }
};
