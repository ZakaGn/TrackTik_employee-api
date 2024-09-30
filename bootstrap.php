<?php

use Dotenv\Dotenv;
use Src\Middleware\CorsMiddleware;

define('ROOT_DIR', __DIR__);
define('ALLOW_CONFIG_ACCESS', true);

require_once ROOT_DIR.'/vendor/autoload.php';

set_error_handler(function ($severity, $message, $file, $line) {
	http_response_code(500);
	echo json_encode(['status' => 'error', 'message' => 'An unexpected error occurred.']);
	error_log("Error: [$severity] $message in $file on line $line");
});

set_exception_handler(function ($exception) {
	http_response_code(500);
	echo json_encode(['status' => 'error', 'message' => 'An unexpected error occurred.']);
	error_log("Exception: " . $exception->getMessage());
});

if(file_exists(ROOT_DIR.'/vendor/autoload.php')){
	require_once ROOT_DIR.'/vendor/autoload.php';
	$dotenv = Dotenv::createImmutable(ROOT_DIR);
	$dotenv->load();
}else{
	echo 'vendor/autoload.php does not exist'.PHP_EOL;
}

CorsMiddleware::handle();
