<?php

namespace Src\Services;

use Exception;
use Src\Mapper\DTOMapper;
use Src\Utils\Auth;

class EmployeeService{
	private Auth $auth;
	private const TRACKTIK_URL = 'https://smoke.staffr.net/rest/v1/employees';

	/**
	 * Constructor to initialize authentication.
	 */
	public function __construct(){
		$this->auth = Auth::getInstance();
	}

	/**
	 * Authenticate the user with the TrackTik API.
	 *
	 * @throws Exception
	 */
	private function authenticate():void{
		try{
			$this->auth->Authentication();
		}catch(Exception $e){
			throw new Exception('Error during authentication: '.$e->getMessage());
		}
	}

	/**
	 * Create a new employee in the TrackTik system.
	 *
	 * @return string Full name of the employee created
	 * @throws Exception
	 */
	public function createEmployee():string{
		$this->authenticate();
		$requestData = $this->receiveEmployeeData();
		$employeeDTO = DTOMapper::mapToDTO($requestData);
		$employeeData = DTOMapper::toTrackTikSchema($employeeDTO);
		$this->sendToTrackTik($employeeData, 'POST');
		return $employeeDTO->getFullName();
	}

	/**
	 * Update an existing employee in the TrackTik system.
	 *
	 * @param int $employeeId The ID of the employee to update
	 * @return bool True if the update was successful
	 * @throws Exception
	 */
	public function updateEmployee(int $employeeId):bool{
		$this->authenticate();
		$requestData = $this->receiveEmployeeData();
		$employeeDTO = DTOMapper::mapToDTO($requestData);
		$employeeData = DTOMapper::toTrackTikSchema($employeeDTO);
		$employeeData['id'] = $employeeId;
		$this->sendToTrackTik($employeeData, 'PUT');
		return true;
	}

	/**
	 * Receive employee data from the incoming HTTP request.
	 *
	 * @return array The received employee data
	 * @throws Exception If the JSON data is invalid
	 */
	private function receiveEmployeeData():array{
		$json = file_get_contents('php://input');
		$requestData = json_decode($json, true);
		if(json_last_error() !== JSON_ERROR_NONE){
			throw new Exception('Invalid JSON data received');
		}
		return $requestData;
	}

	/**
	 * Send employee data to TrackTik using the specified HTTP method.
	 *
	 * @param array $employeeData The employee data to send
	 * @param string $method The HTTP method to use (POST or PUT)
	 * @throws Exception
	 */
	private function sendToTrackTik(array $employeeData, string $method):void{
		try{
			$accessToken = $this->auth->getAccessToken();
			$url = self::TRACKTIK_URL.($method === 'PUT' ? '/'.$employeeData['id'] : '');
			$this->makeRequest($method, $url, $accessToken, $employeeData);
		}catch(Exception $e){
			throw new Exception('Error forwarding data to TrackTik: '.$e->getMessage());
		}
	}

	/**
	 * Make an HTTP request to the TrackTik API.
	 *
	 * @param string $method The HTTP method to use (POST, PUT)
	 * @param string $url The URL to send the request to
	 * @param string|null $accessToken The access token for authentication
	 * @param array|null $data The data to send in the request body
	 * @throws Exception
	 */
	private function makeRequest(string $method, string $url, ?string $accessToken = null, ?array $data = null):void{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$headers = ['Content-Type: application/json'];
		if($accessToken){
			$headers[] = 'Authorization: Bearer '.$accessToken;
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		if($data !== null){
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		}
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if(curl_errno($ch)){
			$error = curl_error($ch);
			curl_close($ch);
			throw new Exception('cURL error: '.$error);
		}
		curl_close($ch);
		if($httpCode >= 400){
			throw new Exception('API Request failed with response: '.$response);
		}
	}

}
