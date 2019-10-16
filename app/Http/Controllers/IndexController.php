<?php

namespace App\Http\Controllers;

use Shea\Component\Http\Request;

class IndexController {

    public function index(Request $request){
        return 'hello world :)';
    }

    public function api(Request $request){
        return response()->json('hello api :)');
    }
}