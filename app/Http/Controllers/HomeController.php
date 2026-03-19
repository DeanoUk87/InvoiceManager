<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Invoices;
use App\User;
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
        if(env('EMAIL_VERIFY')=='ON') {
            $this->middleware(['auth', 'verified']);
        }else{
            $this->middleware(['auth']);
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $users = User::count();
        $customers = Customers::count();
        $invoices = Invoices::count();
        return view('sysadmin.index',compact('user','customers','invoices','users'));
    }
}
