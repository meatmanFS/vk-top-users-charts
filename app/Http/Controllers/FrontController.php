<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\VK_Data;
use App\Settings;

class FrontController extends Controller
{
	/**
	 * Magic methiod to fill in the models instances
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
	
    public function index() {
		$app_data = array(
			'token'		=> csrf_token(),
			'getData'	=> url('/vk-data'),
		);
		return view('front.index', compact('app_data'));
	}
	
	public function vk_data( Request $request ) {
		$this->validate($request, [
            'sort'	=> 'required',
            'order'	=> 'required',
        ]);

		$number_of_data = 30;
		
        $sort		= request()->input('sort');
        $order		= request()->input('order');
        $initial	= (boolean)request()->input('initial');
		if( 'desc' == strtolower( $order ) ){
			$order = 'DESC';
		} elseif( 'asc' == strtolower( $order ) ){
			$order = 'ASC';
		} else {// default
			$order = 'DESC';
		}
		$first_name = array();
		$last_name = array();

		if( 'first_name' == strtolower( $sort ) ){
			$first_name = $this->vk_data->get_first_name( $order, $number_of_data );
			$this->settings->update_option( 'vk_first_name_cache', serialize( $first_name ) );
		} elseif( 'last_name' == strtolower( $sort ) ){
			$last_name = $this->vk_data->get_last_name( $order, $number_of_data );
			$this->settings->update_option( 'vk_last_name_cache', serialize( $last_name ) );
		} 
				
		if( empty( $first_name ) ){
			$vk_first_name_cache = $this->settings->get_option( 'vk_first_name_cache' );
			if( !empty( $vk_first_name_cache ) ){
				$first_name = unserialize( $vk_first_name_cache );	
				if( $first_name->isEmpty() || $initial ){
					$first_name = $this->vk_data->get_first_name( 'DESC', $number_of_data );	
					$this->settings->update_option( 'vk_first_name_cache', serialize( $first_name ) );
				}
			} else {
				$first_name = $this->vk_data->get_first_name( 'DESC', $number_of_data );	
				$this->settings->update_option( 'vk_first_name_cache', serialize( $first_name ) );
			}
		}
		if( empty( $last_name ) ){
			$vk_last_name_cache = $this->settings->get_option( 'vk_last_name_cache' );
			if( !empty( $vk_last_name_cache ) ){
				$last_name = unserialize( $vk_last_name_cache );
				if( $last_name->isEmpty() || $initial ){
					$last_name = $this->vk_data->get_last_name( 'DESC', $number_of_data );	
					$this->settings->update_option( 'vk_last_name_cache', serialize( $last_name ) );
				}
			} else {
				$last_name = $this->vk_data->get_last_name( 'DESC', $number_of_data);
				$this->settings->update_option( 'vk_last_name_cache', serialize( $last_name ) );
			}
		}
		$data = array();		
		foreach ( $first_name as $key => $value) {
			$data[] = array(
				'fisrtName'			=> $value->first_name,
				'fisrtNameCount'	=> $value->first_name_count,
				'lastName'			=> isset( $last_name[ $key ] ) && isset( $last_name[ $key ]->last_name )? $last_name[ $key ]->last_name : '',
				'lastNameCount'		=> isset( $last_name[ $key ] ) && isset( $last_name[ $key ]->last_name_count )? $last_name[ $key ]->last_name_count: '',
			);
		}
		return $data;
	}
}
