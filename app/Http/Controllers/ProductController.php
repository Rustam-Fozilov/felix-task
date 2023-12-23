<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\WarehouseResource;
use App\Models\Warehouses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /* Request jo'natish uchun body forma namunasi request-body.txt faylida ko'rsatilgan */
    public function __invoke(StoreProductRequest $request): JsonResponse
    {
        $productsData = [];

        foreach ($request['products'] as $product) {
            $productData = [
                'product_name' => $product['product_name'],
                'product_qty' => $product['product_qty'],
                'product_materials' => [],
            ];

            /* Mahsulotga tegishli materiallarni qidirish va saqlash */
            foreach ($product['materials'] as $material) {
                $warehouse = Warehouses::with('material')
                    ->where('material_id', '=', $material['id'])
                    ->first();

                /* Ombordan materiallarni izlash */
                list($productData, $warehouse) = $this->checkWarehouse($warehouse, $material, $productData);

                /* Ombordan topilgan materiallarni saqlash */
                list($productData) = $this->saveMaterials($warehouse, $material, $productData);
            }

            $productsData[] = $productData;
        }

        return response()->json($productsData);
    }

    public function checkWarehouse($warehouse, mixed $material, array $productData): array
    {
        if ($warehouse['reminder'] < $material['qty']) {
            $warehouseResource = new WarehouseResource($warehouse);
            $productData['product_materials'][] = $warehouseResource->resolve();

            $warehouse = Warehouses::with('material')
                ->where('material_id', '=', $material['id'])
                ->where('id', '!=', $warehouse['id'])
                ->where('reminder', '>=', $material['qty'])
                ->first();
        }

        return array($productData, $warehouse);
    }

    public function saveMaterials(mixed $warehouse, mixed $material, mixed $productData): array
    {
        if (is_null($warehouse)) {
            /* Agar ombordan yetarlicha miqdorda material topilmasa bor materialni saqlab qo'yish */
            $warehouse = Warehouses::with('material')
                ->where('material_id', '=', $material['id'])
                ->first();

            $warehouse = Warehouses::with('material')
                ->where('material_id', '=', $material['id'])
                ->where('id', '!=', $warehouse['id'])
                ->where('reminder', '<=', $material['qty'])
                ->first();

            $productData['product_materials'][] = [
                'warehouse_id' => null,
                'material_name' => $warehouse['material']['name'],
                'qty' => $material['qty'] - end($productData['product_materials'])['qty'],
                'price' => null,
            ];
        } else {
            /* Ombordan kerakli materiallar topilsa ularni saqlab qo'yish */
            $qty = $material['qty'] - end($productData['product_materials'])['qty'];

            $productData['product_materials'][] = [
                'warehouse_id' => $warehouse['id'],
                'material_name' => $warehouse['material']['name'],
                'qty' => $qty,
                'price' => $warehouse['price'],
            ];
        }

        return array($productData);
    }
}
