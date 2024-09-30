<?php

if(!defined('ALLOW_CONFIG_ACCESS')){
	die('Direct access not allowed.');
}

return [
	'auth' => [
		'client_id' => $_ENV['CLIENT_ID'] ?? null,
		'client_secret' => $_ENV['CLIENT_SECRET'] ?? null,
		'token_endpoint' => $_ENV['TOKEN_ENDPOINT'] ?? null,
		'scope' => $_ENV['SCOPE'] ?? null,
		'username' => $_ENV['USERNAME'] ?? null,
		'password' => $_ENV['PASSWORD'] ?? null,
	]
];
