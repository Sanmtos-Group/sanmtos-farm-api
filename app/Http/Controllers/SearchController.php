<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class SearchController extends Controller
{
    public function search(): \Illuminate\Http\JsonResponse
    {
        $search = Product::where('name', 'LIKE', '%' . request()->name . '%')
            ->when(request()->price, function($query) {
                $query->where('price', request()->price);
            })
            ->when(request()->weight, function($query) {
                $query->where('weight', 'LIKE', '%' . request()->weight . '%');
            })
            ->when(request()->volume, function($query) {
                $query->where('volume', 'LIKE', '%' . request()->volume . '%');
            })
            ->when(request()->created_at, function($query) {
                $query->where('created_at', 'LIKE', '%' . request()->created_at . '%');
            })
            ->limit(10)
            ->get();

        if (count($search) == 0){
            return response()->json([
                'message' => "No product found for your search, try other keywords",
                'data' => null
            ], 200);
        }

        return response()->json([
            'message' => "Ok",
            'data' => $search
        ], 200);

    }

    public function searchable(): \Illuminate\Http\JsonResponse
    {
        $search = QueryBuilder::for(Product::class)
            ->defaultSort('name')
            ->allowedSorts(
                'name',
                'price',
                'weight',
                'volume',
                'discount',
                AllowedSort::custom('recent', new \App\Models\Sorts\LatestSort()),
                AllowedSort::custom('oldest', new \App\Models\Sorts\OldestSort()),
            )
            ->allowedFilters([
                'name',
                'price',
                'created_at',
                AllowedFilter::scope('min_price'),
                AllowedFilter::scope('max_price'),
                AllowedFilter::scope('price_between'),
                AllowedFilter::scope('category'),
                AllowedFilter::scope('store'),
                AllowedFilter::scope('recent'),
            ])
            ->allowedIncludes([
                'rate',
                'category',
                'likes',
                'country',
            ])
            ->paginate()
            ->appends(request()->query());

        $searchable =  ProductResource::collection($search);

        $searchable->with['status'] = "OK";
        $searchable->with['message'] = 'Products retrieved successfully';

        return response()->json([
            'message' => "Ok",
            'data' => $searchable
        ], 200);
    }

    public function productSearch(): \Illuminate\Http\JsonResponse
    {
        $product = Product::where('name', 'LIKE', '%' . request()->name . '%')
            ->when(request()->price, function($query) {
                $query->where('price', request()->price);
            })
            ->when(request()->created_at, function($query) {
                $query->where('created_at', 'LIKE', '%' . request()->created_at . '%');
            })
            ->limit(10)
            ->get();

        if (count($product) == 0){
            return response()->json([
                'message' => "No product found for your search, try other keywords",
                'data' => null
            ], 200);
        }

        return response()->json([
            'message' => "Ok",
            'data' => $product
        ], 200);

    }

    public function categorySearch(): \Illuminate\Http\JsonResponse
    {
        $category = Category::where('name', 'LIKE', '%' . request()->name . '%')
            ->limit(10)
            ->get();

        if (count($category) == 0){
            return response()->json([
                'message' => "No category found for your search, try other keywords",
                'data' => null
            ], 200);
        }

        return response()->json([
            'message' => "Ok",
            'data' => $category
        ], 200);
    }

    public function orderSearch(): \Illuminate\Http\JsonResponse
    {
        $order = Order::where('number', 'LIKE', '%' . request()->number . '%')
            ->when(request()->price, function($query) {
                $query->where('price', request()->price);
            })
            ->when(request()->total_price, function($query) {
                $query->where('total_price', 'LIKE', '%' . request()->total_price . '%');
            })
            ->when(request()->ordered_at, function($query) {
                $query->where('ordered_at', 'LIKE', '%' . request()->ordered_at . '%');
            })
            ->when(request()->created_at, function($query) {
                $query->where('created_at', 'LIKE', '%' . request()->created_at . '%');
            })
            ->limit(10)
            ->get();

        if (count($order) == 0){
            return response()->json([
                'message' => "No order found for your search, try other keywords",
                'data' => null
            ], 200);
        }

        return response()->json([
            'message' => "Ok",
            'data' => $order
        ], 200);
    }

    public function userSearch(): \Illuminate\Http\JsonResponse
    {
        $user = User::where('first_name', 'LIKE', '%' . request()->first_name . '%')
            ->when(request()->last_name, function($query) {
                $query->where('last_name', request()->last_name);
            })
            ->when(request()->email, function($query) {
                $query->where('email', 'LIKE', '%' . request()->email . '%');
            })
            ->when(request()->phone_number, function($query) {
                $query->where('phone_number', 'LIKE', '%' . request()->phone_number . '%');
            })
            ->when(request()->created_at, function($query) {
                $query->where('created_at', 'LIKE', '%' . request()->created_at . '%');
            })
            ->limit(10)
            ->get();

        if (count($user) == 0){
            return response()->json([
                'message' => "No user found for your search, try other keywords",
                'data' => null
            ], 200);
        }

        return response()->json([
            'message' => "Ok",
            'data' => $user
        ], 200);
    }

    public function storeSearch(): \Illuminate\Http\JsonResponse
    {
        $store = Store::where('name', 'LIKE', '%' . request()->first_name . '%')
            ->when(request()->created_at, function($query) {
                $query->where('created_at', 'LIKE', '%' . request()->created_at . '%');
            })
            ->limit(10)
            ->get();

        if (count($store) == 0){
            return response()->json([
                'message' => "No store found for your search, try other keywords",
                'data' => null
            ], 200);
        }

        return response()->json([
            'message' => "Ok",
            'data' => $store
        ], 200);
    }
}
