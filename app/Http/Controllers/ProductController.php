<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Product;  
use Illuminate\Support\Facades\Validator;

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
            'products' => Product::where('user_id',auth()->user()->id)->get(),
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
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required',
            'des' => 'required',
            'product_image' => 'required',
            'product_image.*' => 'image|mimes:jpeg,png,jpg,gif|max:20048',
        ]);

        if ($validator->fails()) {
                  $response = [
        'status' => 'error',
        'message' => $validator->errors(),
      ];
    return response($response, 401);
                }

    if($request->hasFile('product_image')){
      $arr = array();
       $images = $request->product_image;
      foreach ($images as $img) {
        $file = $img;
      $filename = time().'.'.$file->getClientOriginalExtension();
      $file->move(public_path('images/product/'.auth()->user()->id.'/'), $filename);
       //$arr[] = $filename; 
       array_push($arr, $filename);
      }
     }
        $product = Product::create([
         'user_id' => auth()->user()->id,
         'name'    => $request->name ?? '',
         'price'   => $request->price ?? 0.0,
         'des'     => $request->des ?? '',
         'status'  => 1,
         'images'  => json_encode($arr),  
        ]);
        if($product){
        $response = [
        'status' => 'success',
        'product' => $product,
        ];
        return response($response, 200);
      }else{
        return response(['status' => 'error', 'message' => 'Database error'], 401);
      }
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
       
        $product = Product::find($id);
        if($product){
          if($request->hasFile('product_image')){
            
      $arr = array();
      $data = [];
       $images = $request->product_image;
      foreach ($images as $img) {
        $file = $img;
      $filename = time().'.'.$file->getClientOriginalExtension();
      $file->move(public_path('images/product/'.auth()->user()->id.'/'), $filename);
       array_push($arr, $filename);
      }
      $data['images'] = json_encode($arr);
      //delet existing image from path
            foreach (json_decode($product->images) as $dImg) {
                $dfile = url('public/images/product/'.auth()->user()->id).'/'.$dImg;
                if (file_exists($dfile)) {
                    unlink($dfile);
                    } 
             }
     } 
     
        //     
       $data = [
         'name'    => $request->name ?? '',
         'price'   => $request->price ?? 0.0,
         'des'     => $request->des ?? '',
         ];
        $product->update($data);
        $response = [
        'status' => 'success',
        'message' => 'Product Update successful',
         ];
        return response($response, 200);
       }else{
        return response(['status' => 'error', 'message' => 'Product not found in dataset'], 401);
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
        $response = [
        'status' => 'success',
        'products' => Product::where('name', 'like', '%'.$name.'%')->get(),
        ];
        return response($response, 200);
         
    }
}
