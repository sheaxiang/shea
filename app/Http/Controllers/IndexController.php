<?php

namespace App\Http\Controllers;

use Shea\Component\Http\Request;
use Shea\Component\Support\Facades\DB;

class IndexController {

    public function index(Request $request){
        return 'hello world :)';
    }

    public function api(Request $request){
        return 'hello api :)';
    }
}