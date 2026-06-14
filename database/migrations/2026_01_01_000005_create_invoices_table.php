<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id('InvoiceID');
            $table->string('InvoiceNumber', 30)->unique();
            $table->unsignedBigInteger('DOID');
            $table->string('InvoiceDescription', 255)->nullable();
            $table->decimal('Subtotal', 10, 2)->default(0);
            $table->decimal('Tax', 10, 2)->default(0);
            $table->decimal('Discount', 10, 2)->default(0);
            $table->decimal('Penalty', 10, 2)->default(0);
            $table->decimal('TotalAmount', 10, 2)->default(0);
            $table->enum('InvoiceStatus', ['Submitted', 'Finance Review', 'Payment Processing', 'Paid', 'Rejected'])->default('Submitted');
            $table->datetime('SubmittedDate')->nullable();
            $table->timestamps();
            $table->foreign('DOID')->references('DOID')->on('delivery_orders')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
