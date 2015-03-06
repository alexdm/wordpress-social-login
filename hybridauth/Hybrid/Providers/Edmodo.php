<?php
/*!
* HybridAuth
* http://hybridauth.sourceforge.net | https://github.com/hybridauth/hybridauth
*  (c) 2009-2011 HybridAuth authors | hybridauth.sourceforge.net/licenses.html
*/

/**
 * Hybrid_Providers_Edmodo
 *
 * see documentation here: https://developers.edmodo.com/edmodo-connect/edmodo-connect-overview-getting-started/
 */
class Hybrid_Providers_Edmodo extends Hybrid_Provider_Model_OAuth2
{ 
	// default permissions 
	public $scope = "basic read_groups read_connections read_user_email create_messages";

	/**
	 * ID wrappers initializer
	 */
	function initialize() 
	{
		parent::initialize();

		// Provider api end-points
		$this->api->api_base_url  = "https://api.edmodo.com/";
		$this->api->authorize_url = "https://api.edmodo.com/oauth/authorize";
		$this->api->token_url     = "https://api.edmodo.com/oauth/token";

		if( $this->token( "access_token" ) )
		{
			$this->api->curl_header = array( 'Authorization: Bearer ' . $this->token( "access_token" ) );
		}
	}

	/**
	 * load the user profile from the api client
	 */
	function getUserProfile()
	{
		$data = $this->api->api( "users/me" );

		if ( ! isset( $data->id ) ){
			throw new Exception( "User profile request failed! {$this->providerId} returned an invalid response.", 6 );
		}

		$this->user->profile->identifier  = @ $data->id;
		$this->user->profile->displayName = @ $data->username;
		$this->user->profile->firstName   = @ $data->first_name;
		$this->user->profile->lastName    = @ $data->last_name;
		$this->user->profile->description = @ $data->type;
		$this->user->profile->photoURL    = @ $data->avatars->large;
		$this->user->profile->profileURL  = @ $data->url;
		$this->user->profile->email       = @ $data->email;
		$this->user->profile->emailVerified = @ $data->email;

		// $this->user->profile->region      = @ $data->location;
        // locale: "en-GB",
        // timezone: "America/New_York",

		return $this->user->profile;
	}
}
