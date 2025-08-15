<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function dashboard()
    {
        $products = Auth::user()->suppliedProducts;
        return view('supplier.dashboard', compact('products'));
    }

    public function products()
    {
        if (!Auth::user()->can('manage supplier products')) {
            abort(403);
        }
        $products = Auth::user()->suppliedProducts;
        return view('supplier.products', compact('products'));
    }

    public function updateProduct(Request $request, $productId)
    {
        $product = Product::where('supplier_id', Auth::id())->findOrFail($productId);
        $product->update($request->only(['name', 'description', 'regular_price', 'sale_price', 'stock_quantity']));
        return redirect()->back()->with('success', 'Cập nhật sản phẩm thành công!');
    }

    public function orders()
    {
        if (!Auth::user()->can('track supplier orders')) {
            abort(403);
        }
        $orderItems = OrderItem::whereIn('product_id', Auth::user()->suppliedProducts->pluck('id'))->with('order')->get();
        return view('supplier.orders', compact('orderItems'));
    }

    public function requests()
    {
        if (!Auth::user()->can('respond to requests')) {
            abort(403);
        }
        // Giả sử có model RequestImport, thêm logic ở đây
        return view('supplier.requests');
    }
}
