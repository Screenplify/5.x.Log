<?php

namespace App\Models;

use CodeIgniter\Model;

class PingpongModel extends Model {
	protected $DBGroup          = 'default';
	protected $table            = 'ping_log';
	protected $primaryKey       = 'sys_index';
	protected $useAutoIncrement = true;
	protected $insertID         = 0;
	protected $returnType       = 'array';
	protected $useSoftDeletes   = false;
	protected $protectFields    = true;
	protected $allowedFields    = ['id', 'type', 'hw_id', 'timestamp', 'data'];

	// Validation
	protected $validationRules      = [];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks = true;
	protected $beforeInsert   = [];
	protected $afterInsert    = [];
	protected $beforeUpdate   = [];
	protected $afterUpdate    = [];
	protected $beforeFind     = [];
	protected $afterFind      = [];
	protected $beforeDelete   = [];
	protected $afterDelete    = [];

	public function validateKey($key) {
		$keyInfo = model(ApiModel::class)->where('token', $key)->asArray()->first();
		return $keyInfo;
	}

	public function checkPolicy($key, $function) {
		$keyInfo = model(ApiModel::class)->where('token', $key)->asArray()->first();
		if ($keyInfo) {
			$policy = $keyInfo['policy'];
			if (!empty($policy)) {
				$decode_policy = json_decode($policy);
				foreach ($decode_policy as $key => $row) {
					if ($key == $function) {
						$data = true;
						break;
					} else {
						$data = false;
					}
				}
			} else {
				$data = false;
			}
		} else {
			$data = false;
		}

		return $data;
	}

}
