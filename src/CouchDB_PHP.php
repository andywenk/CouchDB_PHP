<?php
/**
 * CouchDB_PHP
 *
 * This is a wrapper for talking to CouchDB written in PHP
 *
 * The requests via HTTP are fired using curl. I assume
 * this library is installed on most servers running PHP
 *
 * Btw: there are no comments because who needs them if the code 
 * is readable enough? If you have questions, just ask me ;-)!
 *
 * copyright (c) 2010 Andy Wenk <andy@nms.de>
 * license: BSD License
 * requires PHP 5.x, php5-curl 
 */
class CouchDB_PHP {
    public $port = 5984;
    public $protocol = 'http';
    public $host = "127.0.0.1";
    protected $last_id;
    protected $db;
    protected $id;
    protected $response_type = 'array';
    protected $request;
	
    public function __construct($db = null) {
        $this->db = $db;
    }
	
    public function set_db($db) {
        $this->db = $db;
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
	
	public function get_last_id() {
	    return $this->last_id;
	}
	
    public function show_all_dbs() {
        $this->request = '_all_dbs/';
		
        return self::parse_response(self::http_request());
    }
	
    public function create_db($name) {
        $this->request = "{$name}/";
        $this->db = $name;
        return self::parse_response(self::http_request('PUT'));
    }
	
    public function delete_db($name) {
        $this->request = "{$name}/";
        return self::parse_response(self::http_request('DELETE'));
    }
	
    public function create_doc($data) {
        if(!self::is_db_set()) return self::error('no_db');
        $method = (empty($this->id)) ? 'POST' : 'PUT'; 
        $this->request = "{$this->db}/{$this->id}/";
        if(!$json_data = self::create_json_data($data)) return self::error('no_json_data');
		$response = self::http_request($method, $json_data);
		
		self::set_last_id($response);
		
        return self::parse_response($response);
    }
    
    protected function set_last_id($response) {
        $response_array = json_decode($response, true);
		$this->last_id = $response_array['id']; 
    }
	
    public function update_doc($data) {
        if(!self::is_db_set()) return self::error('no_db');
        if(!self::is_rev_set($data)) return self::error('no_rev');
        if(!self::is_id_set()) return self::error('no_id');
		
        $this->request = "{$this->db}/{$this->id}/";
        if(!$json_data = self::create_json_data($data)) return self::error('no_json_data');

        return self::parse_response(self::http_request('PUT', $json_data));
    }
	
    public function get_doc() {
        if(!self::is_db_set()) return self::error('no_db');
        if(!self::is_id_set()) return self::error('no_id');
        $this->request = "{$this->db}/{$this->id}/";
		
        return self::parse_response(self::http_request());
    }
	
    public function get_all_docs() {
        if(!self::is_db_set()) return self::error('no_db');
        $this->request = "{$this->db}/_all_docs/";
        return self::parse_response(self::http_request());
    }
	
    public function delete_doc($data) {
        if(!self::is_db_set()) return self::error('no_db'); 
        if(!self::is_rev_set($data)) return self::error('no_rev');
        if(!self::is_id_set()) return self::error('no_id');
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
        return (!$json_data = json_encode($data)) ? false : $json_data;
    }
	
    protected function is_rev_set($data) {
        return (!array_key_exists('_rev', $data)) ? false : true;
    }
	
    protected function is_id_set() {
        return (empty($this->id)) ? false : true;
    }
	
    protected function is_db_set() {
        return (empty($this->db)) ? false : true;
    }
	
    protected function error($type) {
        switch($type) {
            case 'no_db':
                return self::parse_response('{"error":"operation impossible", "reason":"no db set"}');
            break;
			
            case 'no_rev':
                return self::parse_response('{"error":"operation impossible", "reason":"no _rev set"}');
            break;
			
            case 'no_id':
                return self::parse_response('{"error":"operation impossible", "reason":"no _id set"}');
            break;	
			
            case 'no_json_data':
                return self::parse_response('{"error":"operation impossible", "reason":"no json data set"}');
            break;
			
            case 'create_url_no_request':
                return self::parse_response('{"error":"create url impossible", "reason":"no request set"}');
            break;		
        }
    }
}