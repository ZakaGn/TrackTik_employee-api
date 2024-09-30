<?php

require_once __DIR__.'/../bootstrap.php';

use Src\Controller\EmployeeController;

$employeeController = new EmployeeController();

if(php_sapi_name() === 'cli'){
	$requestUri = '/api/employee';
	$requestMethod = 'POST';
	$_SERVER['REQUEST_METHOD'] = $requestMethod;
}else{
	$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	$requestMethod = $_SERVER['REQUEST_METHOD'];
}

try{
	switch($requestMethod){
		case 'POST':
			if($requestUri === '/api/employee'){
				$employeeController->createEmployee();
			}else{
				throw new Exception('Endpoint not found', 404);
			}
			break;
		case 'PUT':
		case 'PATCH':
			if(preg_match('/\/api\/employee\/(\d+)/', $requestUri, $matches)){
				$employeeId = (int)$matches[1];
				$employeeController->updateEmployee($employeeId);
			}else{
				throw new Exception('Endpoint not found', 404);
			}
			break;
		case 'OPTIONS':
			http_response_code(200);
			break;
		default:
			throw new Exception('Method Not Allowed', 405);
	}
}catch(Throwable $e){
	$statusCode = $e instanceof Exception ? $e->getCode() : 500;
	http_response_code($statusCode);
	error_log($e->getMessage().' in '.$e->getFile().' on line '.$e->getLine());
	echo json_encode([
		'status' => 'error',
		'message' => $statusCode === 500 ? 'An unexpected error occurred' : $e->getMessage()
	]);
}
