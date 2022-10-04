<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class Api extends ResourceController {

	use ResponseTrait;

	protected $modelName = 'App\Models\PingpongModel';
	protected $format    = 'json';

	public function device_ping() {

		$request = \Config\Services::request();

		if (!$request->hasHeader('AUTH-KEY')) {
			$response = $this->fail('API key is required', 400);
			return $response;exit;
		}

		$authKey = $request->header('AUTH-KEY');

		if (empty($authKey)) {
			$response = $this->fail('API key is required', 400);
			return $response;exit;
		}

		$splitKey = explode(':', $authKey);
		$key      = trim($splitKey[1]);

		$validKey = $this->model->validateKey($key);

		if (!$validKey) {
			$response = $this->fail('Invalid API key', 400);
			return $response;exit;
		} else if ($validKey['status_id'] == 15) {
			$response = $this->fail('Your API key has been decommissioned', 400);
			return $response;exit;
		} else if ($validKey['status_id'] != 2 && $validKey['status_id'] != 18) {
			$response = $this->fail('Inactive API key', 400);
			return $response;exit;
		}

		$validPolicy = $this->model->checkPolicy($key, 'createDevicePing');

		if (!$validPolicy) {
			$response = $this->fail('This API key does not have access to the requested controller', 400);
			return $response;exit;
		}

		//$item = $request->getPost();
		$json = $request->getJSON();

		if (empty($json)) {
			$response = $this->fail('Requested data is empty', 400);
			return $response;exit;
		}

		$dataArray = [
			'id'        => @$json->id,
			'type'      => @$json->type,
			'hw_id'     => @$json->hw_id,
			'timestamp' => @$json->timestamp,
			'data'      => @$json->data,
		];

		$res = $this->model->insert($dataArray);

		if ($res) {
			$data = ['status_code' => 201, 'status' => true, 'message' => 'success'];
			return $this->respondCreated($data);
		} else {
			$response = $this->fail('Failed to create ping', 400);
			return $response;exit;
		}

	}
}
