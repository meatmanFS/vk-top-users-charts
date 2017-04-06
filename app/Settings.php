<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable = ['name', 'value'];
    public $timestamps = false;

    public static function get_login_url(){
    	$client_id = self::get_client_id();
        if( !empty( $client_id ) ){
            $redirect_uri = self::get_redirect_uri();
            $display = 'page';
            $scope = 'friends,groups';
            $response_type = 'code';
            return "https://oauth.vk.com/authorize?client_id={$client_id}&amp;display={$display}&amp;redirect_uri={$redirect_uri}&amp;scope={$scope}&amp;response_type={$response_type}&amp;v=5.52";
            
        }
        return '';
    }


    public static function get_redirect_uri(){
    	return url( '/dashboard/settings' );
    }

    public static function get_client_id(){
    	$client_id = Settings::where('name', 'client_id')->get()->first();
    	if( !empty( $client_id->value ) ){
    		return $client_id->value;
    	}
    	return '';
    }

    public static function get_client_secret(){
    	$client_secret = Settings::where('name', 'client_secret')->get()->first();
    	if( !empty( $client_secret->value ) ){
    		return $client_secret->value;
    	}
    	return '';
    }
	
    public static function get_access_token(){
    	$access_token = Settings::where('name', 'access_token')->get()->first();
    	if( !empty( $access_token->value ) ){
    		return $access_token->value;
    	}
    	return '';
    }

    public static function check_oauth(){
    	if( !empty( $_GET['code'] ) ){
    		$client = new \GuzzleHttp\Client();
    		$code = $_GET['code'];
    		$client_id = self::get_client_id();
			$client_secret = self::get_client_secret();
			$redirect_uri = self::get_redirect_uri();
			try{
				$res = $client->get("https://oauth.vk.com/access_token?client_id={$client_id}&client_secret={$client_secret}&redirect_uri={$redirect_uri}&code={$code}");
			} catch ( \GuzzleHttp\Exception\ClientException $e ){
				return $e->getMessage();
			}
			
			if( 200 == $res->getStatusCode()){
				$response = json_decode( $res->getBody() );
				if( isset( $response->access_token ) ){
					self::updateOrCreate( 
			            ['name' => 'access_token'],
			            ['value' => $response->access_token ]
			        );
				}
			} 
    	}
    	return false;
    }
}
