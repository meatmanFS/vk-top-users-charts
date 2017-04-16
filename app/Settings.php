<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client as GClient;

class Settings extends Model
{
    protected $fillable = ['name', 'value'];
    public $timestamps = false;

    private $_redirect_url;
    private $_client_id;
    private $_client_secret;
    private $_access_token;

    /**
     * Form the login link to auth in the VK , and get code
     * with it to get the access token
     *
     * @return string
     */
    public function get_login_url(){
    	$client_id = $this->get_client_id();
        if( !empty( $client_id ) ){
            $redirect_uri = $this->get_redirect_uri();
            $display = 'page';
            $scope = 'friends,groups';
            $response_type = 'code';
            return "https://oauth.vk.com/authorize?client_id={$client_id}&amp;display={$display}&amp;redirect_uri={$redirect_uri}&amp;scope={$scope}&amp;response_type={$response_type}&amp;v=5.52";

        }
        return '';
    }

    /**
     * Get the redirect url
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function get_redirect_uri(){
        if( empty( $this->_redirect_url ) ){
            $this->_redirect_url = url( '/dashboard/settings' );
        }
        return $this->_redirect_url;
    }

    /**
     * Get the VK APP client id
     *
     * @return string
     */
    public function get_client_id(){
        if( empty( $this->_client_id ) ){
            $this->_client_id = $this->get_option( 'client_id' );
        }
        return $this->_client_id;
    }

    /**
     * Get the VK APP client secret
     *
     * @return string
     */
    public function get_client_secret(){
        if( empty( $this->_client_secret ) ){
            $this->_client_secret = $this->get_option( 'client_secret' );
        }
        return $this->_client_secret;
    }

    /**
     * Get the VK APP access token
     *
     * @return string
     */
    public function get_access_token(){
        if( empty( $this->_access_token ) ){
            $this->_access_token = $this->get_option( 'access_token' );
        }
        return $this->_access_token;
    }

    /**
     * Get the data from the settings table, where name == $option_name
     *
     * @param $option_name
     * @return string
     */
    public function get_option( $option_name ){
    	$option = $this->where('name', $option_name )->get()->first();
    	if( !empty( $option->value ) ){
    		return $option->value;
    	}
    	return '';
    }

    /**
     * Update or create if not exits the table row, with setted name nad values
     *
     * @param $option_name
     * @param $option_value
     * @return Model
     */
    public function update_option( $option_name, $option_value ){
        return $this->updateOrCreate(
            ['name' => $option_name],
            ['value' => $option_value ]
        );
    }

    /**
     * Try to get the access token on the
     * return from the VK, uses setted by it code param
     *
     * @return bool|string
     */
    public function check_oauth(){
    	if( !empty( $_GET['code'] ) ){
            $client = new GClient();
            $code = $_GET['code'];
            $client_id = $this->get_client_id();
            $client_secret = $this->get_client_secret();
			$redirect_uri = $this->get_redirect_uri();
			try{
				$res = $client->get("https://oauth.vk.com/access_token?client_id={$client_id}&client_secret={$client_secret}&redirect_uri={$redirect_uri}&code={$code}");
			} catch ( \GuzzleHttp\Exception\ClientException $e ){
				return $e->getMessage();
			}

			if( 200 == $res->getStatusCode()){
				$response = json_decode( $res->getBody() );
				if( isset( $response->access_token ) ){
				    $this->update_option('access_token',$response->access_token);
				}
			}
    	}
    	return false;
    }
}
