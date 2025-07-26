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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('tax', 10, 2)->default(0);
            $table->enum('status', ['lunas', 'cicil']);
            $table->enum('payment_method', ['cash', 'bank_transfer']);
            $table->string('bank_name')->nullable();
            $table->string('payment_proof')->nullable();
            $table->string('payment_note')->nullable();
            $table->date('deadline');
            $table->decimal('paid_amount', 10, 2)->nullable();
            $table->decimal('ppn', 10, 2)->nullable();
            $table->decimal('pph', 10, 2)->nullable();
            $table->unsignedBigInteger('client_id');
            $table->timestamps();

            // foreign key
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
