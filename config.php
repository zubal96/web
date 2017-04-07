<?php

class config{


static $app_id = '5944941';
static $secret_key = 'vXuvd4TNRMze7rWQbY2V';
static $params_code = array(
'client_id' => '5944941',
 'display' => 'page',
 'redirect_uri' => 'http://localhost/site/index.php',
 'scope' => 'friends,groups',
 'response_type' => 'code',
 'v' => '5.63'
);

static $get_access_code_url = 'http://oauth.vk.com/authorize?';
static $get_access_token_url = 'https://oauth.vk.com/access_token?';

function GetToken() {
$params = array(
'client_id' => config::$app_id,
 'client_secret' => config::$secret_key,
 'redirect_uri' => 'http://localhost/site/index.php',
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
