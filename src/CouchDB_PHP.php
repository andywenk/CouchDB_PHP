<?php
/**
 * CouchDB_PHP
 *
 * This is a wrapper for talking to CouchDB written in PHP
 *
 * copyright (c) 2010 Andy Wenk <andy@nms.de>
 * license: BSD License
 * requires PHP 5.x
 */
class CouchDB_PHP {
	protected $port = 5984;
	protected $protocol = 'http';
	protected $host = "127.0.0.1";
	protected $db;
	protected $request;
	protected $id;
	
	public function __construct() {}
	
	public function set_port($port) {
		$this->port = $port;
	}
	
	public function set_protocol($protocol) {
		$this->protocol = $protocol;
	}
	
	public function set_host($host) {
		$this->host = $host;
	}
	
	public function set_db($db) {
		$this->db = $db;
	}
	
	public function set_id($id = '') {
		$this->id = $id;
	}
	
	public function get_id() {
		if(empty($this->id)) {
			$this->request = '_uuids/';
			$id_arr = self::parse_response(self::http_request());
			return $id_arr['uuids'][0];
		}
		
		return $this->id;
	}
	
	public function set_data($data) {
		try {
			if(!$this->json_data = json_encode($data)) throw new Exception('set_data: not able to encode the data to JSON'); 
		} catch (Exception $e){
			echo $e->getMessage();
			return false;
		}
		
		return true;
	}
	
	public function show_all_dbs() {
		$this->request = '_all_dbs/';
		
		return self::parse_response(self::http_request());
	}
	
	public function create_db($name) {
		$this->request = "{$name}/";
		return self::parse_response(self::http_request('PUT'));
	}
	
	public function delete_db($name) {
		$this->request = "{$name}/";
		return self::parse_response(self::http_request('DELETE'));
	}
	
	public function create_doc() {
		
	}
	
	public function update_doc() {
		
	}
	
	public function delete_doc() {
		
	}
	
	public function http_request($type = 'GET') {
		$ch = curl_init();
		
		if($type == 'PUT' || $type == 'POST') {
			if(!empty($this->json_data)) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $this->json_data);
			}
		}
		
		curl_setopt($ch, CURLOPT_URL, self::create_url());
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'CouchDB_PHP');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
		
		$result = curl_exec($ch);
		curl_close($ch);
		
		return $result;
	}
	
	protected function create_url() {
		return "{$this->protocol}://{$this->host}:{$this->port}/{$this->request}";
	}
	
	protected function parse_response($response) {
		return json_decode($response, true);
	}
	
}