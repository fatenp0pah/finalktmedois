<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id('DOID');
            $table->string('DONumber', 30)->unique();
            $table->unsignedBigInteger('VendorID');
            $table->string('PONumber', 30)->nullable();
            $table->string('ProjectReference', 50)->nullable();
            $table->string('Customer', 100)->nullable();
            $table->text('ShippingAddress')->nullable();
            $table->text('InvoiceAddress')->nullable();
            $table->string('ItemNo', 50)->nullable();
            $table->text('ItemDescription')->nullable();
            $table->integer('Quantity')->default(1);
            $table->date('DeliveryDate')->nullable();
            $table->time('DeliveryTime')->nullable();
            $table->string('DOFileLink', 255)->nullable();
            $table->string('ProofFileLink', 255)->nullable();
            $table->enum('DOStatus', ['Draft', 'Submitted', 'Under Review', 'Approved', 'Rejected'])->default('Draft');
            $table->text('Remark')->nullable();
            $table->datetime('SubmittedDate')->nullable();
            $table->timestamps();
            $table->foreign('VendorID')->references('VendorID')->on('vendors')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('delivery_orders');
    }
};
