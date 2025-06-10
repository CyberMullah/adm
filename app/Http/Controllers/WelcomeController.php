<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Number;

final class WelcomeController
{
    public function __invoke()
    {
        return view('welcome', compact('components', 'downloads', 'contributors'));
    }
}
