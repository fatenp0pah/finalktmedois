<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('UserID');
            $table->string('Username', 100);
            $table->string('UserPassword', 255);
            $table->string('UserEmail', 100)->unique();
            $table->enum('UserRole', ['Vendor', 'Officer', 'Finance', 'Admin']);
            $table->enum('UserStatus', ['Active', 'Inactive'])->default('Active');
            $table->datetime('LastLogin')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
