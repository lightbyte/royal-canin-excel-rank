<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Mostrar la página principal
     */
    public function index()
    {
        return view('pages.home');
    }
}
