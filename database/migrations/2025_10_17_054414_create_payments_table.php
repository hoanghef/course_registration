<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('registration_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'bank_transfer', 'e_wallet', 'other'])->default('cash');
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->string('transaction_code', 50)->unique()->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};