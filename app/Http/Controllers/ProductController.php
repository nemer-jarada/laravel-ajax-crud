<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function products()
    {
        $products = Product::latest()->paginate(5);
        return view('product', compact('products'));
    }
    // Add Product
    public function addProduct(Request $request)
    {
        $request->validate(
            [
                'name' => 'required|unique:products',
                'price' => 'required',
            ],
            // Error Custom MSG
            [
                'name.required' => 'Name Is Required',
                'name.unique' => 'Product Already Exsis',
                'price.required' => 'Price Is Required',
            ]
        );
        $product = new Product();
        $product->name = $request->name;
        $product->price = $request->price;
        $product->save();
        return response()->json([
            'status' => 'success',
        ]);
    }

    // Update Product
    public function updateProduct(Request $request)
    {
        $request->validate(
            [
                'up_name' => 'required|unique:products,name,' . $request->up_id,
                'up_price' => 'required',
            ],
            // Error Custom MSG
            [
                'up_name.required' => 'Name Is Required',
                'up_name.unique' => 'Product Already Exsis',
                'up_price.required' => 'Price Is Required',
            ]
        );

        Product::where('id', $request->up_id)->update([
            'name' => $request->up_name,
            'price' => $request->up_price,
        ]);


        return response()->json([
            'status' => 'success',
        ]);
    }
    public function deleteProduct(Request $request)
    {
        Product::findOrFail($request->product_id)->delete();
        return response()->json([
            'status' => 'success',
        ]);
    }
    // pagination
    public function pagination(Request $request)
    {
        $products = Product::latest()->paginate(5);
        return view('pagination_product', compact('products'))->render();
    }
    // Search
    public function searchProduct(Request $request)
    {
        $products = Product::where('name', 'like', '%' . $request->search_string . '%')
            ->orWhere('price', 'like', '%' . $request->search_string . '%')
            ->orderBy('id', 'desc')
            ->paginate(5);

        if ($products->count() >= 1) {
            return view('pagination_product', compact('products'))->render();
        } else {
            return response()->json([
                'status' => 'nothing_found'
            ]);
        }
    }
}
