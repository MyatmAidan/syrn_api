<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('skin_type_id')->nullable()->after('ingredients');
            $table->unsignedInteger('qty')->default(0)->after('price');
            $table->json('images')->nullable()->after('description');
            $table->timestamp('updated_at')->nullable()->after('created_at');

            $table->foreign('skin_type_id')
                ->references('skin_type_id')
                ->on('skin_types')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });

        $skinTypeMap = [];
        $distinctTypes = DB::table('products')
            ->whereNotNull('skin_type')
            ->where('skin_type', '!=', '')
            ->distinct()
            ->pluck('skin_type');

        foreach ($distinctTypes as $name) {
            $id = DB::table('skin_types')->insertGetId([
                'name' => $name,
                'description' => null,
                'created_at' => now(),
            ]);
            $skinTypeMap[$name] = $id;
        }

        foreach ($skinTypeMap as $name => $skinTypeId) {
            DB::table('products')
                ->where('skin_type', $name)
                ->update(['skin_type_id' => $skinTypeId]);
        }

        $productsWithImage = DB::table('products')
            ->whereNotNull('product_image')
            ->where('product_image', '!=', '')
            ->get();

        foreach ($productsWithImage as $product) {
            DB::table('products')
                ->where('product_id', $product->product_id)
                ->update([
                    'images' => json_encode([$product->product_image]),
                ]);
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['skin_type', 'product_image']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('skin_type', 100)->nullable()->after('ingredients');
            $table->string('product_image', 255)->nullable()->after('description');
        });

        foreach (DB::table('products')->orderBy('product_id')->get() as $product) {
            $skinTypeName = null;
            if ($product->skin_type_id) {
                $skinTypeName = DB::table('skin_types')
                    ->where('skin_type_id', $product->skin_type_id)
                    ->value('name');
            }

            $images = $product->images ? json_decode($product->images, true) : null;
            $firstImage = is_array($images) && count($images) > 0 ? $images[0] : null;

            DB::table('products')
                ->where('product_id', $product->product_id)
                ->update([
                    'skin_type' => $skinTypeName,
                    'product_image' => $firstImage,
                ]);
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['skin_type_id']);
            $table->dropColumn(['skin_type_id', 'qty', 'images', 'updated_at']);
        });
    }
};
