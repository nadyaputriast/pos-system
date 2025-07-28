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
        Schema::create('feedback', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('invoice_id');
        $table->unsignedBigInteger('product_id');
        $table->unsignedBigInteger('client_id')->nullable();
        $table->integer('rating')->nullable();
        $table->text('comment')->nullable();
        $table->timestamps();

        // foreign key
        $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
