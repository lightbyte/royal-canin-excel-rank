<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrivacyController extends Controller
{
    /**
     * Mostrar página de política de privacidad
     */
    public function index()
    {
        return view('pages.privacy');
    }
}
