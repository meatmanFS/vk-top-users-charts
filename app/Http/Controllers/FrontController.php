<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\VK_Data;

class FrontController extends Controller
{
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

        $sort	= request()->input('sort');
        $order	= request()->input('order');
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
			$first_name = VK_Data::get_first_name( $order );
		} elseif( 'last_name' == strtolower( $sort ) ){
			$last_name = VK_Data::get_last_name( $order );
		} 

		if( empty( $first_name ) ){
			$first_name = VK_Data::get_first_name();
		}
		if( empty( $last_name ) ){
			$last_name = VK_Data::get_last_name();
		}
		$data = array();
		foreach ( $first_name  as $key => $value) {
			$data[] = array(
				'fisrtName'			=> $value->first_name,
				'fisrtNameCoutn'	=> $value->first_name_count,
				'lastName'			=> isset( $last_name[ $key ]->last_name )? $last_name[ $key ]->last_name : '',
				'lastNameCount'		=> isset( $last_name[ $key ]->last_name_count )? $last_name[ $key ]->last_name_count: '',
			);
		}
		return $data;
	}
}
