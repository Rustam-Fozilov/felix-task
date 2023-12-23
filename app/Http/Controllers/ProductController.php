<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Material;
use App\Models\Warehouses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function store(StoreProductRequest $request)
    {
        $product_material = null;
        $product_name = $request['product_name'];
        $product_qty = $request['product_qty'];
        $materials = $request['materials'];

        $response = Warehouses::query()
            ->join('materials', 'materials.id', '=', 'warehouses.material_id')
            ->where('materials.id', '=', 1)
            ->get();

        if ($response[0]['reminder'] >= $product_qty) {
           $response[0]['qty'] = $product_qty;
        } else {
            $product_material = $response[0]['reminder'];
        }
//        return response()->json('');
    }
}
