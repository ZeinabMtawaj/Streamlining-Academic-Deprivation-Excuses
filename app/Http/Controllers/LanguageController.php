<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switchLang(Request $request)
    {
        $lang = $request->input('language');
        if (in_array($lang, ['en', 'ar'])) {
            session()->put('locale', $lang);
        }
        return redirect()->back();
    }
}
