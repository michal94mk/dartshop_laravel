<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\PaymentStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('payment_id')->nullable(); // External payment ID
            $table->string('payment_method');
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('PLN');
            $table->string('status')->default(PaymentStatus::PENDING->value);
            $table->text('transaction_data')->nullable(); // JSON data for payment gateway response
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
