<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routine_steps', function (Blueprint $table) {
            $table->increments('step_id');
            $table->unsignedInteger('routine_id');
            $table->unsignedInteger('product_id');
            $table->integer('step_order');
            $table->text('instruction')->nullable();

            $table->foreign('routine_id')
                  ->references('routine_id')
                  ->on('routines')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('products')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routine_steps');
    }
};
