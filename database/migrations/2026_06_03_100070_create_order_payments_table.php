<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_payments', function (Blueprint $table) {
            $table->increments('order_payment_id');
            $table->unsignedInteger('order_id')->unique();
            $table->unsignedInteger('payment_bank_id');
            $table->decimal('amount', 12, 2);
            $table->string('slip_image', 500);
            $table->string('status', 20)->default('pending');
            $table->unsignedInteger('reviewed_by_admin_id')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('order_id')
                ->references('order_id')
                ->on('orders')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('payment_bank_id')
                ->references('payment_bank_id')
                ->on('payment_banks')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('reviewed_by_admin_id')
                ->references('admin_id')
                ->on('admins')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
