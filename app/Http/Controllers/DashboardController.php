<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Settings;

class DashboardController extends Controller
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
        return view('dashboard.index');
    }

    public function settings(){
        $login_url      = Settings::get_login_url();
        $client_id      = Settings::get_client_id();
        $client_secret  = Settings::get_client_secret();
        $oauth_error    = Settings::check_oauth();
        $access_token    = Settings::get_access_token();

        return view('dashboard.settings', compact('client_id','login_url','client_secret','oauth_error','access_token'));
    }

    public function store( Request $request ){
        $this->validate($request, [
            'client_id'     => 'required',
            'client_secret' => 'required',
        ]);

        $client_id = request()->input('client_id');
        $client_secret = request()->input('client_secret');
        
        Settings::updateOrCreate( 
            ['name' => 'client_id'],
            ['value' => $client_id ]
        );
        Settings::updateOrCreate( 
            ['name' => 'client_secret'],
            ['value' => $client_secret ]
        );
        return back();
    }
}
