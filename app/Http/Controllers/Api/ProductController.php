<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseController;
use App\Models\{Product, ProductAttribute};
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    
    /**
     * Products list
     * @return Json
     */
    public function list(){
        try { 
            $productList = Product::with(['attributes'=> function($q){
                $q->select('id', 'product_id','name', 'stock','price');
            }])->select('*')
                ->status()->get();
           
            return $this->sendResponse($productList, 'Listing fetched successfully');
        } catch (\Exception $ex) {
            \Log::warning('Error while fetch product'. $ex->getMessage(). $ex->getLine());
            return $this->sendError($ex->getMessage());
        }
    }

    /**
     * Product add
     */
    public function store(Request $request){
        try { 
            $request->validate([
                'name' => 'required',
                'description' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if($file = $request->file('image')) { 
                $path = 'images/';
                $fileName   = time() . $file->getClientOriginalName();
                \Storage::disk('public')->put('images/' . $fileName, \File::get($file));
                $file_name  = $file->getClientOriginalName();
                $file_type  = $file->getClientOriginalExtension();
                $filePath   = $path . $fileName;     
            }
            $product  = new Product();
            $product->name  = $request->name;
            $product->slug  = $request->name;
            $product->description = $request->description;
            $product->image  = $fileName;
            $product->save();          

            if(isset($request->size)){
                foreach($request->size as $key => $size){
                    $attributes = new ProductAttribute();
                    $attributes->product_id = $product->id;
                    $attributes->name = $size;
                    $attributes->price =  @$request->price[$key];
                    $attributes->stock =  @$request->stock[$key];
                    $attributes->color = @$request->color[$key];
                    $attributes->save();
                }
            }                 
            
           return $this->sendResponse([], 'Product added successfully');
        } catch (\Exception $ex) {
            \Log::warning('Error while adding product'. $ex->getMessage(). $ex->getLine());
            return $this->sendError($ex->getMessage());
        }
    }

    /**
     * Update product
     */
    public function update(Request $request){
        try{
            $request->validate([
                'name' => 'required',
                'description' => 'required'
            ]);
            $product = Product::find($request->product_id);
            if($file = $request->file('image')) { 
                $path = 'images/';
                $fileName   = time() . $file->getClientOriginalName();
                \Storage::disk('public')->put('images/' . $fileName, \File::get($file));
                $file_name  = $file->getClientOriginalName();
                $file_type  = $file->getClientOriginalExtension();
                $filePath   = $path . $fileName;
                $product->image  = $fileName;     
            }
            $product->name  = $request->name;
            $product->slug  = $request->name;
            $product->description = $request->description;
            
            $product->save();      
            return $this->sendResponse([], 'Product updated successfully'); 
        }catch (\Exception $ex) {
            \Log::warning('Error while updating product'. $ex->getMessage(). $ex->getLine());
            return $this->sendError($ex->getMessage());
        }
    }
    /**
     * Delete product
     */

     public function delete(Request $request){
        try{
            Product::destroy($request->id);
            return $this->sendResponse([], 'Product deleted successfully'); 
        }catch (\Exception $ex) {
            \Log::warning('Error while deleting product'. $ex->getMessage(). $ex->getLine());
            return $this->sendError($ex->getMessage());
        }
     }
}
