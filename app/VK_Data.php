<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Settings;

class VK_Data extends Model
{
	protected $table = 'vk_data';
	protected $fillable = [
			'vk_id', 'first_name', 'last_name', 'sex', 'nickname', 
			'screen_name', 'bdate', 'city', 'status', 'followers_count',			
			'home_town', 'activities', 'personal'		
		];	
	public static $country = 2;// Ukrane
	public static $count = 1000;// All posble (VK limit)
	public static $token = 1000;// access token
	public static $client;// \GuzzleHttp\Client
	
	public static function run_import($import_number ){
		try {
			self::_run_import( $import_number );
		}catch ( \Exception $ex ) {
			return $ex->getMessage();
		}
		return true;
	}	
	
	private static function _run_import( $import_number ){
		self::$token = Settings::get_access_token();
		self::$client = new \GuzzleHttp\Client();
		if( !empty( self::$token ) ){
			set_time_limit(360);
			$citi_id = self::get_city_id();
			if( !empty( $citi_id ) ){
				$sort = array(1,0);
				$sex = array(1,2);
				$users_ids = array();
				
				$data = array();
				$all_response = self::get_users( $citi_id );
				if( 
					!empty( $all_response ) 
					&& is_array( $all_response->response ) 
					&& ( count( $all_response->response ) < 800 )
				){
					$parsed_response =self::parse_response( $all_response, $users_ids );
					$data = array_merge( $data, $parsed_response->data );
					$users_ids = array_merge( $users_ids, $parsed_response->users_ids );
					sleep(3);
				} else { // if there are more than 800 users breack to sub requests	
					// check for the data that we fetch before, if it is ok ,parse it
					sleep(3);
					if( !empty( $all_response ) && is_array( $all_response->response ) ){
						$parsed_response =self::parse_response( $all_response, $users_ids );
						$data = array_merge( $data, $parsed_response->data );
						$users_ids = array_merge( $users_ids, $parsed_response->users_ids );						
					}
					foreach ( $sort as $sort_item ){
						foreach ( $sex as $sex_item ){
							$response = self::get_users( $citi_id, $sort_item, $sex_item );
							sleep(3);//dont call to vk in 3 sec, in order to avoid fail (vk req)
							$parsed_response =self::parse_response( $response, $users_ids );
							$data = array_merge( $data, $parsed_response->data );
							$users_ids = array_merge( $users_ids, $parsed_response->users_ids );						
						}
					}
				}
				$count = self::count();
				if( $count < $import_number ){
					try {
						self::insert( $data );							
					} catch ( \Exception $ex) {
						return self::stop_import();
					}
				} elseif( $count >= $import_number) {
					
					return self::stop_import();
				}
				
			} 
		}
		return self::stop_import();
	}
	
	public static function parse_response( $response, $users_ids ){
		$data = array();
		if( !empty( $response ) && isset( $response->response ) && is_array( $response->response ) ){
			foreach ( $response->response as $item ){
				if( !isset( $item->uid ) ){
					continue;
				}
				if( in_array( $item->uid, $users_ids ) ){
					continue;
				}
				$_data = self::parse_data( $item );
				$users_ids[] = $item->uid;
				if(!self::where('vk_id', '=', $_data['vk_id'])->exists()) {
					$data[] = $_data;
				}
			}					
		}
		return (object)array( 
			'data'		=> $data,
			'users_ids'	=> $users_ids
		);
	}
	
	public static function get_city_id(){
		$cities = self::get_cities();
		if( is_array( $cities ) ){
			$city_id = array_shift( $cities );
			Settings::updateOrCreate( 
				['name' => 'vk_cities'],
				['value' => serialize( $cities ) ]
			);
			return $city_id;
		}
		return false;
		 
	}
	
	public static function stop_import(){
		return 'Import Stoped!';
	}
	
	public static function get_users( $city, $sort = '', $sex = '' ){
		$fields = 'sex,bdate,city,home_town,status,followers_count,nickname,'
			. 'personal,connections,exports,activities,screen_name,maiden_name';
		try{
			$requst_url = sprintf( 
					"https://api.vk.com/method/users.search?country=%s"
						. "&city=%s&count=%s&fields=%s&access_token=%s",
					self::$country,
					$city,					
					self::$count,
					$fields,
					self::$token
			);
			if( !empty( $sort ) ){
				$requst_url .= sprintf( '&sort=%s', $sort );
			}
			if( !empty( $sex ) ){
				$requst_url .= sprintf( '&sex=%s', $sex );
			}
			$res = self::$client->get( $requst_url );
		} catch ( \GuzzleHttp\Exception\ClientException $e ){
			return false;
		}
		return json_decode( $res->getBody() );
	}	
	
	public static function parse_data( $data ){
		$new_data = array();
		$fields = array(
			'vk_id', 'first_name', 'last_name', 'sex', 'nickname', 
			'screen_name', 'bdate', 'city', 'status', 'followers_count',			
			'home_town', 'activities', 'personal'
		);
		foreach ( $fields as $item  ){
			if( 'vk_id' == $item ){
				$new_data['vk_id'] = $data->uid;
				continue;
			} 
			
			if( isset( $data->$item ) ){
				$_new_data = $data->$item;				
				if( is_array( $_new_data ) || is_object( $_new_data ) ){
					$new_data[ $item ] = serialize( $data->$item );	
				} else {
					$new_data[ $item ] = $data->$item;									
				}
			} else {
				$new_data[ $item ] = '';
			}
		}
		return $new_data;
	}
	
	public static function get_regions(){
		try{
			$res = self::$client->get(
				sprintf( 
					"https://api.vk.com/method/database.getRegions?country_id=%s"
						. "&count=%s&access_token=%s",
					self::$country,
					self::$count,
					self::$token
				)
			);
		} catch ( \GuzzleHttp\Exception\ClientException $e ){
			return false;
		}
		return json_decode( $res->getBody() );
	}
	
	public static function get_region_cities( $region_id ){
		try{
			$res = self::$client->get(
				sprintf( 
					"https://api.vk.com/method/database.getCities?country_id=%s"
						. "&region_id=%s&need_all=0&count=%s&access_token=%s",
					self::$country,
					$region_id,
					self::$count,
					self::$token
				)
			);
		} catch ( \GuzzleHttp\Exception\ClientException $e ){
			return false;
		}
		return json_decode( $res->getBody() );
	}
	
	public static function get_cities(){
		$vk_cities = Settings::get_option('vk_cities');
		if( !empty( $vk_cities )  ){
			return unserialize( $vk_cities );
		}
		set_time_limit(360);
		$regions = self::get_regions();
		sleep(3);//dont call to vk in 3 sec, in order to avoid fail (vk req)
		if( 
			!empty( $regions ) 
			&& isset( $regions->response ) 
			&& is_array( $regions->response ) 
		){
			$cities = array();
			foreach ( $regions->response as $region ){
				if( isset( $region->region_id ) ){
					$region_cities = self::get_region_cities( $region->region_id );
					sleep(3);
					if( 
						!empty( $region_cities ) 
						&& isset( $region_cities->response ) 
						&& is_array( $region_cities->response )
					){
						foreach ( $region_cities->response as $region_city ){
							/**
							 * cid => (int) 1500682
							  title => (string) Агрономическое
							  area => (string) Винницкий район
							  region => (string) Винницкая область
							 */
							if( isset( $region_city->cid ) ){
								$cities[] = $region_city->cid;
							}				
						}
					}
				}				
			}
			Settings::updateOrCreate( 
				['name' => 'vk_cities'],
				['value' => serialize( $cities ) ]
			);
			return $cities;
		}
		return false;
	}
}
