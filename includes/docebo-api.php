<?php

// Calling the Docebo API

class Api {

	// These will be overwritten from the plugin options in the WordPress admin area
	
	static public $url 			= '';
	static public $key 			= '';
	static public $secret_key 	= '';

	static public $sso 			= '';

	static public function getHash($params) {
		global $dwp_options;
		
		$key = ((isset($dwp_options['docebo_key']) && !empty($dwp_options['docebo_key'])) ? $dwp_options['docebo_key'] : self::$key);
		$secret_key = ((isset($dwp_options['docebo_secret']) && !empty($dwp_options['docebo_secret'])) ? $dwp_options['docebo_secret'] : self::$secret_key);
		
		$res =array('sha1'=>'', 'x_auth'=>'');

		$res['sha1']=sha1(implode(',', $params) . ',' . $secret_key);

		$res['x_auth']=base64_encode($key . ':' . $res['sha1']);

		return $res;
	}

	static private function getDefaultHeader($x_auth) {
		global $dwp_options;
		
		$cloudUrl = ((isset($dwp_options['docebo_address']) && !empty($dwp_options['docebo_address'])) ? $dwp_options['docebo_address'] : self::$url);
		
		if(!empty($cloudUrl)){
			$host = parse_url('http://'.$cloudUrl, PHP_URL_HOST);
			return array(
				"Host: " . ($host ? $host : ''),
				"Content-Type: multipart/form-data",
				'X-Authorization: Docebo '.$x_auth,
			);
		}
		
		return array();
		
	}

	static public function call($action, $data_params) {
		
		global $dwp_options;
		
		// Import the URL from the plugin settings or from this file otherwise
		$cloudUrl = 'http://'.((isset($dwp_options['docebo_address']) && !empty($dwp_options['docebo_address'])) ? $dwp_options['docebo_address'] : self::$url);

		$curl = curl_init();

		$hash_info = self::getHash($data_params);
		$http_header =self::getDefaultHeader($hash_info['x_auth']);

		$opt = array(
			CURLOPT_URL=>$cloudUrl . '/api/' . $action,
			CURLOPT_RETURNTRANSFER=>1,
			CURLOPT_HTTPHEADER=>$http_header,
			CURLOPT_POST=>1,
			CURLOPT_POSTFIELDS=>$data_params,
			CURLOPT_CONNECTTIMEOUT=>5, // Timeout to 5 seconds
		);
		curl_setopt_array($curl, $opt);

		// $output contains the output string
		$output = curl_exec($curl);

		// it closes the session
		curl_close($curl);

		return $output;
	}

	static public function sso($user) {
		
		global $dwp_options;
		
		$cloudUrl = ((isset($dwp_options['docebo_address']) && !empty($dwp_options['docebo_address'])) ? $dwp_options['docebo_address'] : self::$url);
		$sso_key = ((isset($dwp_options['docebo_sso']) && !empty($dwp_options['docebo_sso'])) ? $dwp_options['docebo_sso'] : self::$sso);

		$time = time();
		$token = md5($user.','.$time.','.$sso_key);

		return 'http://' . $cloudUrl . '/doceboLms/index.php?modname=login&op=confirm&login_user=' . strtolower($user) . '&time=' . $time . '&token=' . $token;

	}

}

?>