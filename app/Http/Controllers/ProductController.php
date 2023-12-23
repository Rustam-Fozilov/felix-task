<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\WarehouseResource;
use App\Models\Warehouses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __invoke(StoreProductRequest $request): JsonResponse
    {
        $productsData = [];

        foreach ($request['products'] as $product) {
            $productData = [
                'product_name' => $product['product_name'],
                'product_qty' => $product['product_qty'],
                'product_materials' => [],
            ];

            foreach ($product['materials'] as $material) {
                $warehouse = Warehouses::with('material')
                    ->where('material_id', '=', $material['id'])
                    ->first();

                if ($warehouse['reminder'] < $material['qty']) {
                    $warehouseResource = new WarehouseResource($warehouse);
                    $productData['product_materials'][] = $warehouseResource->resolve();

                    $warehouse = Warehouses::with('material')
                        ->where('material_id', '=', $material['id'])
                        ->where('id', '!=', $warehouse['id'])
                        ->where('reminder', '>=', $material['qty'])
                        ->first();
                }

                if (is_null($warehouse)) {
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
                    $qty = $material['qty'] - end($productData['product_materials'])['qty'];

                    $productData['product_materials'][] = [
                        'warehouse_id' => $warehouse['id'],
                        'material_name' => $warehouse['material']['name'],
                        'qty' => $qty,
                        'price' => $warehouse['price'],
                    ];
                }
            }

            $productsData[] = $productData;
        }

        return response($productsData);
    }
}
