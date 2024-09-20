<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function get()
    {
        return view("login");
    }
    public function post()
    {
        return  isset($_POST['what']);
    }
}