<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $user = Auth::user();
        if ($user->user_type == 0) {
            return redirect('/admin/merchants');
        }
        if ($user->user_type == 1) {
            return redirect('/merchant-admin/clients');
        }
        if ($user->user_type == 2) {
            return redirect('/merchant-manager/clients');
        }
        if ($user->user_type == 3) {
            return redirect('/client/shop');
        }
        // return view('home');
    }

    public function test()
    {
        return view('dashboard.test');
    }
}
