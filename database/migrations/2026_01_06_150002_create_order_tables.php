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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->enum('status', ['new', 'confirmed', 'shipped', 'delivered', 'canceled'])->default('new');

            $table->string('customer_name');
            $table->string('phone');
            $table->string('phone2')->nullable();
            $table->string('governorate');
            $table->text('address');
            $table->text('notes')->nullable();

            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_total', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('payment_method')->default('cod');

            $table->foreignId('created_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->decimal('unit_price', 10, 2);
            $table->unsignedInteger('qty');
            $table->decimal('line_subtotal', 10, 2);
            $table->decimal('line_discount', 10, 2)->default(0);
            $table->decimal('line_total', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};

