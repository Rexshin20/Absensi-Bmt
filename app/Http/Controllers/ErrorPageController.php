<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ErrorPageController extends Controller
{
    public function unauthorized(): View
    {
        return view('pages.errors.401');
    }

    public function notFound(): View
    {
        return view('pages.errors.404');
    }

    public function serverError(): View
    {
        return view('pages.errors.500');
    }
}
