<?php

class config{


	static $app_id = '5961268';
	static $secret_key = '1zcI0ZaxnISXHWulbY4q';
	static $params_code = array(
		'client_id' => '5961268',
		'group_ids' => '88212219',
		'display' => 'page',
		'redirect_uri' => 'http://web-2/examples/index.php',
		'response_type' => 'code',
		'scope' => 'messages,photos',
		'v' => '5.63',
		'state' =>'group'
		);

	static $params_code_user = array(
		'client_id' => '5961268',
		'display' => 'page',
		'redirect_uri' => 'http://web-2/examples/index.php',
		'response_type' => 'code',
		'scope' => 'friends',
		'v' => '5.63',
		'state' =>'user'
		);

	static $get_access_code_url = 'http://oauth.vk.com/authorize?';
	static $get_access_token_url = 'https://oauth.vk.com/access_token?';


	function GetTokenUser() {
		$params = array(
			'client_id' => config::$app_id,
			'client_secret' => config::$secret_key,
			'redirect_uri' => 'http://web-2/examples/index.php',
			'code' => $_SESSION['code_user']
			);
		$url = 'https://oauth.vk.com/access_token?' . http_build_query($params);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$token = json_decode(curl_exec($ch));
		curl_close($ch);
		return $token;
	}

	function GetToken() {
		$params = array(
			'client_id' => config::$app_id,
			'client_secret' => config::$secret_key,
			'group_ids' => '88212219',
			'redirect_uri' => 'http://web-2/examples/index.php',
			'code' => $_SESSION['code']
			);
		$url = 'https://oauth.vk.com/access_token?' . http_build_query($params);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$token = json_decode(curl_exec($ch));
		curl_close($ch);
		return $token;
	}
}
