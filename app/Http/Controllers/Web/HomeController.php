<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class HomeController extends Controller {
    public function index(int $number): ?string {
        return str_repeat('hello world', $number);
    }
}
