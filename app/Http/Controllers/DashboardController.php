<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Settings;
use App\VK_Data;

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
		$items_number	=	Settings::get_option( 'vk_users_number' );
		$items_number	=	!empty( $items_number )? $items_number: 100000;// default be 100000 :)
		$items_сount	= VK_Data::count();		
		$persent		= ( ( $items_сount /( $items_number / 100 )  ) < 100 ) ? $items_сount /( $items_number / 100 ): 100;
				
		$import_data = array(
			'startImportUrl'	=> url('/dashboard/start-import/'),
			'token'				=> csrf_token(),
			'itemsNumber'		=> $items_number,
			'itemsCount'		=> $items_сount,
		);
        return view('dashboard.index', compact( 'import_data','items_number','items_сount', 'persent' ));
    }

    public function settings(){
        $login_url			= Settings::get_login_url();
        $client_id			= Settings::get_client_id();
        $client_secret		= Settings::get_client_secret();
        $oauth_error		= Settings::check_oauth();
        $access_token		= Settings::get_access_token();
		$vk_users_number	=	Settings::get_option( 'vk_users_number' );
		$vk_users_number	=	!empty( $vk_users_number )? $vk_users_number: 100000;// default be 100000 :)

        return view('dashboard.settings', compact('client_id','login_url','client_secret','oauth_error','access_token','vk_users_number'));
    }

    public function store( Request $request ){
        $this->validate($request, [
            'client_id'     => 'required',
            'client_secret' => 'required',
        ]);

        $client_id			= request()->input('client_id');
        $client_secret		= request()->input('client_secret');
        $vk_users_number	= request()->input('vk_users_number');
        
        Settings::updateOrCreate( 
            ['name' => 'client_id'],
            ['value' => $client_id ]
        );
        Settings::updateOrCreate( 
            ['name' => 'client_secret'],
            ['value' => $client_secret ]
        );
        Settings::updateOrCreate( 
            ['name' => 'vk_users_number'],
            ['value' => $vk_users_number ]
        );
        return back();
    }
	
	public function start_import( Request $request ) { 
		if( VK_Data::count() < $request->input('itemsNumber') ){
			$import = VK_Data::run_import(  $request->input('itemsNumber') );
			if( $import === true ){
				return response()->json( ['result' => 'success', 'message'=> "Import started! Fetching users, please wait.", 'items_count'=> VK_Data::count() ] );				
			} else {
				return response()->json( ['result' => 'fail', 'message'=> $import] );								
			}
		} else {
			return response()->json( ['result' => 'fail', 'message'=> 'Row alredy Imported!' ] );			
		}
		return response()->json( ['result' => 'fail', 'message'=> 'Something wrong!' ] );
	}
}
