<?php

namespace Src\Controller;

use Exception;
use Src\Services\EmployeeService;

class EmployeeController{
	private EmployeeService $employeeService;

	public function __construct(){
		$this->employeeService = new EmployeeService();
	}

	/**
	 * Handle creating a new employee.
	 */
	public function createEmployee():void{
		try{
			$employeeFullName = $this->employeeService->createEmployee();
			http_response_code(201);
			echo json_encode([
				'status' => 'success',
				'message' => "Employee '$employeeFullName' created successfully"
			]);
		}catch(Exception $e){
			$this->handleError($e);
		}
	}

	/**
	 * Handle updating an existing employee.
	 *
	 * @param int $employeeId
	 */
	public function updateEmployee(int $employeeId):void{
		try{
			$this->employeeService->updateEmployee($employeeId);
			http_response_code(200);
			echo json_encode([
				'status' => 'success',
				'message' => "Employee with ID '$employeeId' updated successfully"
			]);
		}catch(Exception $e){
			$this->handleError($e);
		}
	}

	/**
	 * Handle errors and send the appropriate response.
	 *
	 * @param Exception $e
	 */
	private function handleError(Exception $e):void{
		$statusCode = $e->getCode() ?: 500;
		http_response_code($statusCode);
		echo json_encode([
			'status' => 'error',
			'message' => $e->getMessage()
		]);
	}
}
