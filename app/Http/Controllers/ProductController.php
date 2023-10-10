<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Product;  

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = [
        'status' => 'success',
            'products' => Product::all(),
        ];
        return response($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required'
        ]);
        Product::create($request->all());
        $response = [
        'status' => 'success',
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $valid = Product::find($id);
        if($valid){
        $response = [
        'status' => 'success',
            'product' => Product::find($id),
        ];
        return response($response, 200);
    }else{
        return response(['status' => 'error'], 401);
    }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $product = Product::find($id);
        if($product){
        $product->update($request->all());
        $response = [
        'status' => 'success',
         ];
        return response($response, 200);
       }else{
        return response(['status' => 'error'], 401);
       }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        Product::destroy($id);
        $response = [
        'status' => 'success',
        ];
        return response($response, 200);
    }

    /**
     * Search for product.
     *
     * @param  str  $name
     * @return \Illuminate\Http\Response
     */
    public function search($name)
    {
        //
        return Product::where('name', 'like', '%'.$name.'%')->get(); 
    }
}
