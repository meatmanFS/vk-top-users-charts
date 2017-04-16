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
	 * Magic method to fill in the models instances
	 * 
	 * @param string $name
	 */
	public function __get($name) {
		switch ( $name ){
			case 'settings':
				$this->settings = new Settings();
			break;
			case 'vk_data':
				$this->vk_data = new VK_Data();
			break;
		}
		return $this->$name;
	}

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$items_number	=	$this->get_items_number();
		$items_сount	= $this->vk_data->count();		
		$persent		= ( ( $items_сount /( $items_number / 100 )  ) < 100 ) ? $items_сount /( $items_number / 100 ): 100;
		$access_token	= (boolean)$this->settings->get_access_token();		
		$import_data = array(
			'startImportUrl'	=> url('/dashboard/start-import/'),
			'token'				=> csrf_token(),
			'itemsNumber'		=> $items_number,
		);
        return view('dashboard.index', compact( 'import_data','items_number','items_сount', 'persent', 'access_token' ));
    }

    /**
     * Settings route
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function settings(){
        $login_url			= $this->settings->get_login_url();
        $client_id			= $this->settings->get_client_id();
        $client_secret		= $this->settings->get_client_secret();
        $oauth_error		= $this->settings->check_oauth();
        $access_token		= $this->settings->get_access_token();
		$vk_users_number	= $this->get_items_number();

        return view('dashboard.settings', compact('client_id','login_url','client_secret','oauth_error','access_token','vk_users_number'));
    }

    /**
     * Help route
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function help() {
		 return view('dashboard.help');
	}

    /**
     * Get the users import number
     *
     * @return int
     */
	public function get_items_number() {
		$items_number	=	$this->settings->get_option( 'vk_users_number' );
		return !empty( $items_number )? $items_number: 100000;// default be 100000 :)
	}

    /**
     * Save the settings
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store( Request $request ){
        $this->validate($request, [
            'client_id'     => 'required',
            'client_secret' => 'required',
        ]);

        $client_id			= request()->input('client_id');
        $client_secret		= request()->input('client_secret');
        $vk_users_number	= request()->input('vk_users_number');
        
        $this->settings->update_option( 'client_id', $client_id );
        $this->settings->update_option( 'client_secret', $client_secret );
        $this->settings->update_option( 'vk_users_number', $vk_users_number );
        return back();
    }

    /**
     * The ajax callback for vk users data import
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function start_import(Request $request ) {
		$items_number	=	$this->get_items_number();
		if( $this->vk_data->count() < $items_number ){
			return $this->vk_data->run_import( $items_number );
		} else {
			return response()->json( ['result' => 'fail', 'message'=> 'Row already Imported!' ] );
		}
		return response()->json( ['result' => 'fail', 'message'=> 'Something wrong!' ] );
	}
}
