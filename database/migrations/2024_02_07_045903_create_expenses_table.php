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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('job_order')->nullable();
            $table->string('payment_to')->nullable();
            $table->date('date');
            $table->string('type')->nullable();
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->string('payment_mode')->nullable();
            $table->text('payment_details')->nullable();
            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
