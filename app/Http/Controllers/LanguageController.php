<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    /**
     * Change the application language.
     *
     * @param  Request  $request
     * @param  string  $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchLang($locale)
    {
        // Vérifier si la langue est supportée
        if (!in_array($locale, ['en', 'fr'])) {
            $locale = 'fr'; // Langue par défaut
        }

        // Stocker la langue choisie en session
        session(['locale' => $locale]);

        // Changer la langue de l'application
        App::setLocale($locale);

        // Rediriger vers la page précédente
        return redirect()->back();
    }
}
