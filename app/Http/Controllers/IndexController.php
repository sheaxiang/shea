<?php

namespace App\Http\Controllers;

use Shea\Component\Http\Request;

class IndexController {

    public function index(Request $request){
        dd(\App::basePath());
        return 'hello world';
    }
}