<?php

/**
* CodeIgniter Wordpress Library by vairam refer in http://developer.wordpress.com/
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program. If not, see <http://www.gnu.org/licenses/>.
*
* @category Wordpress
* @package CodeIgniter
* @subpackage Client
* @version 1.0
* @license http://www.gnu.org/licenses/ GNU General Public License
*/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wordpress_api {

/*
* Variable to hold an insatance of CodeIgniter so we can access CodeIgniter features
*/
protected $codeigniter_instance;

/*
* Create an array of the urls to be used in api calls
* The urls contain conversion specifications that will be replaced by sprintf in the functions
* @var string
*/

    /*
* Construct function
* Sets the codeigniter instance variable and loads the lang file
*/
    function __construct() {
    
     // Set the CodeIgniter instance variable
     $this->codeigniter_instance =& get_instance();
    
     // Load the Wordpress API language file
     $this->codeigniter_instance->load->config('config');
    
    }
    
    /*
* Create a variable to hold the Oauth access token
* @var string
*/
    public $access_token = FALSE;
    
    /*
* Function to create the login with Wordpress link
* @return string Wordpress login url
*/
    function wordpressLogin() {
    
	$wpcc_state = md5( mt_rand() );

	$_SESSION[ 'wpcc_state' ] = $wpcc_state;
	
     return 'https://public-api.wordpress.com/oauth2/authorize/?client_id=' . $this->codeigniter_instance->config->item('wordpress_client_id') . '&redirect_uri=' . $this->codeigniter_instance->config->item('wordpress_redirect_url') .'&response_type=code&state='.$wpcc_state;
    
    }
    
    /*
* The api call function is used by all other functions
* It accepts a parameter of the url to use
* And an optional string of post parameters
* @param string api url
* @param array post parameters for curl call
* @return std_class data returned form curl call
*/
    function __apiCall($url, $post_parameters = FALSE) {
    	
     // Initialize the cURL session
$curl_session = curl_init();

// Set the URL of api call
curl_setopt($curl_session, CURLOPT_URL, $url);

// If there are post fields add them to the call
if($post_parameters !== FALSE) {
curl_setopt ($curl_session, CURLOPT_POSTFIELDS, $post_parameters);
}

curl_setopt($curl_session, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, 0);

// Return the curl results to a variable
curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, 1);

// Execute the cURL session
$contents = curl_exec ($curl_session) or die(" Not execute the curl");

// Close cURL session
curl_close ($curl_session);

// Return the response
return json_decode($contents);
    
    }
    
    /*
* The authorize function to get the OAuth token
* Accepts a code that is returned from Wordpress to our redirect url
* @param string code generated by Wordpress when the user has been sent to our redirect url
* @return std_class Wordpress OAuth data
*/
function authorize($code)
{

$authorization_url = 'https://public-api.wordpress.com/oauth2/token';


return $this->__apiCall($authorization_url, "client_id=" . $this->codeigniter_instance->config->item('wordpress_client_id') . "&client_secret=" . $this->codeigniter_instance->config->item('wordpress_client_secret') . "&redirect_uri=" . $this->codeigniter_instance->config->item('wordpress_redirect_url') . "&grant_type=authorization_code&code=" . $code);


}

function getUserdetails($access_token)
{
	
$curl = curl_init( "https://public-api.wordpress.com/rest/v1/me/" );
curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Authorization: Bearer ' . $access_token ) );
curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1);

curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

return $me = json_decode( curl_exec( $curl ) );

}

function me_details($access_token)
{
	$options  = array (
  'http' =>
  array (
    'ignore_errors' => true,
    'header' =>
    array (
      0 => 'authorization: Bearer'.$access_token,
    ),
  ),
);
 
$context  = stream_context_create( $options );
$response = file_get_contents(
  'https://public-api.wordpress.com/rest/v1/me/?pretty=1',
  false,
  $context
);
$response = json_decode( $response );

return $response;

}

function post($username)
{
	
$authorization_url = 'https://public-api.wordpress.com/rest/v1/sites/'.$username.'.wordpress.com/posts?number=5&pretty=1';

echo $authorization_url;

$curl_session = curl_init();

// Set the URL of api call
curl_setopt($curl_session, CURLOPT_URL, $authorization_url);

curl_setopt($curl_session, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, 0);


curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, 1);

// Execute the cURL session
$contents = curl_exec ($curl_session) or die(" Not execute the curl");

// Close cURL session
curl_close ($curl_session);

// Return the response
return json_decode($contents);
	
}
}?>