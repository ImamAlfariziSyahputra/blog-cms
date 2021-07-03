<?php

namespace App\Http\Controllers;

class LocalizationController extends Controller
{
    public function switchLang($language = 'en')
    {
        request()->session()->put('locale', $language);
        return redirect()->back();
    }
}
