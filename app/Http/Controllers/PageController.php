<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    public function dashboard(): View
    {
        return view('dashboard.index');
    }

    public function accountSettings(): View
    {
        return view('pages.account-settings');
    }

    public function cards(): View
    {
        return view('pages.cards');
    }

    public function tables(): View
    {
        return view('pages.tables');
    }

    public function typography(): View
    {
        return view('pages.typography');
    }

    public function formLayouts(): View
    {
        return view('pages.form-layouts');
    }

    public function icons(): View
    {
        return view('pages.icons');
    }

    public function errorPage(): View
    {
        return view('pages.errors.404')->with('layout', 'blank');
    }
}
