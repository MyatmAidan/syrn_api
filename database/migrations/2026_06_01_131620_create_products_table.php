<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('product_id');
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('brand_id')->nullable();
            $table->unsignedInteger('admin_id')->nullable();
            $table->string('product_name', 150);
            $table->text('ingredients')->nullable();
            $table->string('skin_type', 100)->nullable();
            $table->string('skin_concern', 255)->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('product_image', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('category_id')
                  ->references('category_id')
                  ->on('categories')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('brand_id')
                  ->references('brand_id')
                  ->on('brands')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('admin_id')
                  ->references('admin_id')
                  ->on('admins')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
