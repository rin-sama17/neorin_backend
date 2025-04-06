<?php

namespace App\Http\Controllers\App\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Product\ProductsCollection;
use App\Models\Product\Products;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
        public function index(){
        return new ProductsCollection(Products::all());
    }
}
