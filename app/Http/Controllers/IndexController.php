<?php

namespace App\Http\Controllers;

use App\Http\Models\Index;

class IndexController extends BaseController
{
    public static function index()
    {
        dd(Index::all());

                
    }
}