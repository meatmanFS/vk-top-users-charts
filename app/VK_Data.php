<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Settings;
use DB;

class VK_Data extends Model
{
	protected $table = 'vk_data';
	protected $fillable = [
			'vk_id', 'first_name', 'last_name', 'sex', 'nickname', 
			'screen_name', 'bdate', 'city', 'status', 'followers_count',			
			'home_town', 'activities', 'personal'		
		];	
	public $country = 2;// Ukraine
	public $count = 1000;// All possible (VK limit)
	public $token = '';// access token
	public $client;// \GuzzleHttp\Client

    /**
     * The settings model class
     * @var Settings
     */
    private $_settings;
    /**
     * The get users request filds
     * @var string
     */
    private $_users_fields = 'sex,bdate,city,home_town,status,followers_count,nickname,'
    . 'personal,connections,exports,activities,screen_name,maiden_name';
    /**
     * The db fields to parse
     * @var array
     */
    private $_fields = array(
        'vk_id', 'first_name', 'last_name', 'sex', 'nickname',
        'screen_name', 'bdate', 'city', 'status', 'followers_count',
        'home_town', 'activities', 'personal'
    );
    /**
     * The vk api url
     * @var string
     */
    private $_vk_api_url = 'https://api.vk.com';
    /**
     * The methods path in vk api url
     * @var string
     */
    private $_vk_api_method_path = '/method/';
    /**
     * The vk methods list
     * @var array
     */
    private $_methods = array(
        'users_search'  => 'users.search',
        'get_regions'   => 'database.getRegions',
        'get_cities'    => 'database.getCities',
    );

    /**
	 * Settings instance 
	 *
     * @return Settings
	 */
	public function get_setings() {
	    if( empty( $this->_settings ) ){
            $this->_settings = new Settings();
        }
		return $this->_settings;
	}

    /**
     * Run the import with using the try statement.
     * Will return true or error message
     *
     * @param $import_number
     * @return bool|string
     */
	public function run_import($import_number ){
		try {
			$import = $this->_run_import( $import_number );
		}catch ( \Exception $ex ) {
			return $this->_import_fail( $ex->getMessage() );
		}
		return $import;
	}

    /**
     * Rut the actual users data import,
     * by breaking the request to the each city
     *
     * @param $import_number
     * @return string
     */
	private function _run_import( $import_number ){
		$settings = $this->get_setings();
		$this->token = $settings->get_access_token();
		$this->client = new \GuzzleHttp\Client();
		if( !empty( $this->token ) ){// check if user is auth
            $count = $this->count();
            // check the limit for the number of users
            if( $count >= $import_number) {// we have the enough data already
                return $this->_import_success( $this->get_stop_import_message() );
            }
			set_time_limit(360);
			// get the city id from in db serialized string
			$city_id = $this->get_city_id();
			if( !empty( $city_id ) ){
				$sort = array(1,0);// sort by registration date and popularity
				$sex = array(1,2);// sort by sex male/female
				$users_ids = array();// the exclude the users that we has saved in this run
				$data = array();
				$from = 0;
				// make call to VK api
				$all_response = $this->get_users( $city_id );
				// check for errors
				if( isset( $all_response->error ) && isset( $all_response->error->error_msg ) ){
                    return $this->_import_fail( 'Message from VK: ' . $all_response->error->error_msg );
				}
				// if there are all ok with response and the are less than 800 users
                // data returned, than save it
				if( 
					!empty( $all_response ) 
					&& isset( $all_response->response )
					&& is_array( $all_response->response ) 
					&& ( count( $all_response->response ) < 800 )
				){
					$parsed_response =$this->parse_response( $all_response, $users_ids );
                    $from += $parsed_response->from;
					$data = array_merge( $data, $parsed_response->data );
					$users_ids = array_merge( $users_ids, $parsed_response->users_ids );
					sleep(3);
				} else { // if there are more than 800 users break to sub requests
					// check for the data that we fetch before, if it is ok ,parse it
					sleep(3);
					if( !empty( $all_response ) && isset( $all_response->response ) && is_array( $all_response->response ) ){
						$parsed_response =$this->parse_response( $all_response, $users_ids );
                        $from += $parsed_response->from;
						$data = array_merge( $data, $parsed_response->data );
						$users_ids = array_merge( $users_ids, $parsed_response->users_ids );						
					}
					foreach ( $sort as $sort_item ){
						foreach ( $sex as $sex_item ){
							$response = $this->get_users( $city_id, $sort_item, $sex_item );
							sleep(3);//don't call to vk in 3 sec, in order to avoid fail (vk req)
							$parsed_response =$this->parse_response( $response, $users_ids );
                            $from += $parsed_response->from;
							$data = array_merge( $data, $parsed_response->data );
							$users_ids = array_merge( $users_ids, $parsed_response->users_ids );						
						}
					}
				} 
				if( !empty( $data ) ){
					// run the adding of the users data from the VK
					try {
						$this->insert( $data );
					} catch ( \Exception $ex) {
                        return $this->_import_fail(  $this->get_stop_import_message() . ' ' . $ex->getMessage() );
                    }
                    return $this->_import_success(  sprintf( 'The #%s users added from: #%s (including existing) !', count( $data ),$from ) );
				} else {
				    if( empty( $from ) ){
				        return $this->_import_fail( sprintf( 'VK not returned users data, consider to stop import and try later!', $from ) );
                    } else {
                        return $this->_import_success( sprintf( 'No data to add, from received #%s users!', $from ) );
                    }
                }
            }
		} else {
            $this->_import_fail( 'No access token, please login to VK at settings page!' );
        }
	}

    private function _import_success( $message ){
        return array(
            'result'        => 'success',
            'message'       => $message,
            'items_count'   => $this->count(),
        );
    }

    private function _import_fail( $message ){
        return array(
            'result'    => 'fail',
            'message'   => $message,
        );
    }

    /**
     * Make the array of db columns => values
     *
     * @param $response
     * @param $users_ids The array of the uses id's that already has been saved
     * @return object
     */
	public function parse_response( $response, $users_ids ){
		$data = array();
		$from = 0;
		if( !empty( $response ) && isset( $response->response ) && is_array( $response->response ) ){
			foreach ( $response->response as $item ){
				if( !isset( $item->uid ) ){
					continue;
				}
                $from++;
				if( in_array( $item->uid, $users_ids ) ){// we have user already, skip it
					continue;
				}
				$_data = $this->parse_data( $item );
				$users_ids[] = $item->uid;
				// check for the in db existence of user, if not exists, add to the data array
				if(!$this->where('vk_id', '=', $_data['vk_id'])->exists()) {
					$data[] = $_data;
				}
			}
		}
		return (object)array(
			'data'		=> $data,
			'users_ids'	=> $users_ids,
            'from'      => $from
		);
	}

    /**
     * Parse the vk response , according to the db fields
     *
     * @param $data
     * @return array
     */
    public function parse_data( $data ){
        $new_data = array();
        foreach ( $this->_fields as $item  ){
            if( 'vk_id' == $item ){
                $new_data['vk_id'] = $data->uid;
                continue;
            }
            if( isset( $data->$item ) ){// we have the db field with returned vk users data
                $_new_data = $data->$item;
                if( is_array( $_new_data ) || is_object( $_new_data ) ){
                    $new_data[ $item ] = serialize( $data->$item );
                } else {
                    $new_data[ $item ] = $data->$item;
                }
            } else {// no such db field , make it empty
                $new_data[ $item ] = '';
            }
        }
        return $new_data;
    }

    /**
     * Get the vk users from $city ,
     * with sort by registration date $sort and $sex
     *
     * @param $city
     * @param string $sort
     * @param string $sex
     * @return bool|mixed
     */
    public function get_users( $city, $sort = '', $sex = '' ){
        try{
            $request_url = sprintf(
                "%s?country=%s&city=%s&count=%s&fields=%s&access_token=%s",
                $this->_get_method('users_search'),
                $this->country,
                $city,
                $this->count,
                $this->_users_fields,
                $this->token
            );
            if( !empty( $sort ) ){
                $request_url .= sprintf( '&sort=%s', $sort );
            }
            if( !empty( $sex ) ){
                $request_url .= sprintf( '&sex=%s', $sex );
            }
            $res = $this->client->get( $request_url );
        } catch ( \GuzzleHttp\Exception\ClientException $e ){
            return false;
        }
        return json_decode( $res->getBody() );
    }

    /**
     * Get the first city id from the settings,
     * and save it back for the afterward usage
     *
     * @return bool|mixed
     */
	public function get_city_id(){
		$settings = $this->get_setings();
		$cities = $this->get_cities();
		if( is_array( $cities ) ){
			$city_id = array_shift( $cities );
			$settings->update_option( 'vk_cities', serialize( $cities ) );
			return $city_id;
		}
		return false;

	}

    /**
     * Get the on import stop message string
     *
     * @return string
     */
	public function get_stop_import_message(){
		return 'Import is Stop!';
	}

    private function _get_method( $name ){
	    $method = isset( $this->_methods[$name] )?$this->_methods[$name]:'';
        return "{$this->_vk_api_url}{$this->_vk_api_method_path}{$method}";
    }

    /**
     * Make call to the vk api to get the regions
     *
     * @return bool|mixed
     */
	public function get_regions(){
		try{
			$res = $this->client->get(
				sprintf( 
					"%s?country_id=%s&count=%s&access_token=%s",
					$this->_get_method('get_regions'),
					$this->country,
					$this->count,
					$this->token
				)
			);
		} catch ( \GuzzleHttp\Exception\ClientException $e ){
			return false;
		}
		return json_decode( $res->getBody() );
	}

    /**
     * Get the cities for the given region
     *
     * @param $region_id
     * @return bool|mixed
     */
	public function get_region_cities( $region_id ){
		try{
			$res = $this->client->get(
				sprintf( 
					"%s?country_id=%s&region_id=%s&need_all=0&count=%s&access_token=%s",
					$this->_get_method('get_cities'),
					$this->country,
					$region_id,
					$this->count,
					$this->token
				)
			);
		} catch ( \GuzzleHttp\Exception\ClientException $e ){
			return false;
		}
		return json_decode( $res->getBody() );
	}

    /**
     * Get all cities
     *
     * @return array|bool|mixed
     */
	public function get_cities(){
		$settings = $this->get_setings();
		$vk_cities = $settings->get_option('vk_cities');
		if( !empty( $vk_cities )  ){
			return unserialize( $vk_cities );
		}
		set_time_limit(360);
		$regions = $this->get_regions();
		sleep(3);//dont call to vk in 3 sec, in order to avoid fail (vk req)
		if( 
			!empty( $regions ) 
			&& isset( $regions->response ) 
			&& is_array( $regions->response ) 
		){
			$cities = array();
			foreach ( $regions->response as $region ){
				if( isset( $region->region_id ) ){
					$region_cities = $this->get_region_cities( $region->region_id );
					sleep(3);
					if( 
						!empty( $region_cities ) 
						&& isset( $region_cities->response ) 
						&& is_array( $region_cities->response )
					){
						foreach ( $region_cities->response as $region_city ){
							if( isset( $region_city->cid ) ){
								$cities[] = $region_city->cid;
							}				
						}
					}
				}				
			}
			$settings->update_option('vk_cities', serialize( $cities ) );
			return $cities;
		}
		return false;
	}

    /**
     * Get the most common first names
     *
     * @param string $order
     * @param int $limit
     * @return mixed
     */
	public function get_first_name( $order = 'DESC', $limit = 10 ){
		return $this->select( 'first_name' , DB::raw('count(*) as first_name_count') )
			->groupBy('first_name')->orderBy( 'first_name_count', $order )->limit( $limit )
			->get();
	}

    /**
     * Get most common last names
     *
     * @param string $order
     * @param int $limit
     * @return mixed
     */
	public function get_last_name( $order = 'DESC', $limit = 10 ){
		return $this->select( 'last_name' , DB::raw('count(*) as last_name_count') )
			->groupBy('last_name')->orderBy( 'last_name_count', $order )->limit( $limit )
			->get();
	}
}
