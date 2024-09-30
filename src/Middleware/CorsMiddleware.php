<?php

namespace Src\Middleware;

class CorsMiddleware{
	public static function handle():void{
		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
		header('Access-Control-Allow-Headers: Content-Type, Authorization');
	}
}
