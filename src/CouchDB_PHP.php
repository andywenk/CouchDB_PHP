<?php
/**
 * CouchDB_PHP
 *
 * This is a wrapper for talking to CouchDB written in PHP
 *
 * The requests via HTTP are fired using curl. I assume
 * this library is installed on most servers running PHP
 *
 * copyright (c) 2010 Andy Wenk <andy@nms.de>
 * license: BSD License
 * requires PHP 5.x, php5-curl
 */
class CouchDB_PHP {
	protected $db;
	protected $port = 5984;
	protected $protocol = 'http';
	protected $host = "127.0.0.1";
	protected $id;
	protected $response_type = 'array';
	protected $request;
	
	
	public function __construct($db = null) {
		$this->db = $db;
	}
	
	public function set_port($port) {
		$this->port = $port;
	}
	
	public function set_protocol($protocol) {
		$this->protocol = $protocol;
	}
	
	public function set_host($host) {
		$this->host = $host;
	}
	
	public function set_id($id) {
		$this->id = urlencode($id);
	}
	
	public function set_response_type($type) {
		$this->response_type = $type;
	}
	
	public function get_uuid() {
		$this->request = '_uuids/';
		$id_arr = self::parse_response(self::http_request());
		
		return $id_arr['uuids'][0];
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
	
	public function create_doc($data) {
		$method = (empty($this->id)) ? 'POST' : 'PUT'; 
		$this->request = "{$this->db}/{$this->id}/";
		$json_data = self::create_json_data($data);
		
		return self::parse_response(self::http_request($method, $json_data));
	}
	
	public function update_doc($data) {
		if(!self::is_rev_set($data)) return self::error('update_no_rev');
		if(!self::is_id_set()) return self::error('update_no_id');
		
		$this->request = "{$this->db}/{$this->id}/";
		$json_data = self::create_json_data($data);

		return self::parse_response(self::http_request('PUT', $json_data));
	}
	
	public function get_doc() {
		if(!self::is_id_set()) return self::error('update_no_id');
		$this->request = "{$this->db}/{$this->id}/";
		
		return self::parse_response(self::http_request());
	}
	
	public function get_all_docs() {
		$this->request = "{$this->db}/_all_docs/";
		return self::parse_response(self::http_request());
	}
	
	public function delete_doc($data) {
		if(!self::is_rev_set($data)) return self::error('update_no_rev');
		if(!self::is_id_set()) return self::error('update_no_id');
		$this->request = "{$this->db}/{$this->id}?rev={$data['_rev']}" ;
		
		return self::parse_response(self::http_request('DELETE'));
	}
	
	public function http_request($type = 'GET', $json_data = '') {
		$ch = curl_init();

		if($type == 'PUT' || $type == 'POST') {
			if(!empty($json_data)) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
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
		if(empty($this->request)) return self::error('create_url_no_request');
		return "{$this->protocol}://{$this->host}:{$this->port}/{$this->request}";
	}
	
	protected function parse_response($response) {
		return ($this->response_type == 'array') ? json_decode($response, true) : $response;
	}
	
	protected function create_json_data($data) {
		if(!$json_data = json_encode($data)) return false; 
		
		return $json_data;
	}
	
	protected function is_rev_set($data) {
		if(!array_key_exists('_rev', $data)) return false;

		return true;
	}
	
	protected function is_id_set() {
		if(empty($this->id)) return false;
		
		return true;
	}
	
	protected function error($type) {
		switch($type) {
			case 'update_no_rev':
				return self::parse_response('{"error":"update impossible", "reason":"no _rev set"}');
			break;
			
			case 'update_no_id':
				return self::parse_response('{"error":"update impossible", "reason":"no _id set"}');
			break;	
			
			case 'create_url_no_request':
				return self::parse_response('{"error":"create url impossible", "reason":"no request set"}');
			break;		
		}
	}
	
}