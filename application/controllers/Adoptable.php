<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Adoptable extends CI_Controller {

	public function index()
	{
		$data = ['pets' => $this->getPets()];

		$this->load->view('inc/header');
		$this->load->view('adoptable', $data);
		$this->load->view('inc/footer');
	}

	function getPets()
	{
		//$petfinder_id = '32288544'; // wonka
		$petfinder_id = 'PA660'; // fur kid rescue
		$key = $this->config->item('key', 'petfinder_api');
		$secret = $this->config->item('secret', 'petfinder_api');
		$token_sig = md5($secret . 'format=json&key=' . $key);

		//xml
		//$petfinder_xml = simplexml_load_string(file_get_contents('http://api.petfinder.com/auth.getToken?key=' . $key . '&sig=' . $token_sig));
		//$token = $petfinder_xml->auth->token;
		//$wonka = simplexml_load_string(file_get_contents('http://api.petfinder.com/pet.get?key=' . $key . '&id=32288544&token=' . $token . '&sig=' . $request_sig));
		
		//JSON with file_get_contents
		/*
		$token_json = json_decode(file_get_contents('http://api.petfinder.com/auth.getToken?format=json&key=' . $key . '&sig=' . $token_sig));
		$token = $token_json->{'petfinder'}->{'auth'}->{'token'}->{'$t'};
		$request_sig = md5($secret . 'format=json&key=' . $key . '&id=' . $petfinder_id . '&token=' . $token);
		$furkid = json_decode(file_get_contents('http://api.petfinder.com/shelter.getPets?format=json&key=' . $key . '&id=' . $petfinder_id . '&token=' . $token . '&sig=' . $request_sig));
		*/

		$token_json = json_decode($this->curl_download('http://api.petfinder.com/auth.getToken?format=json&key=' . $key . '&sig=' . $token_sig));
		$token = $token_json->{'petfinder'}->{'auth'}->{'token'}->{'$t'};
		$request_sig = md5($secret . 'format=json&key=' . $key . '&id=' . $petfinder_id . '&token=' . $token);
		$furkid_json = json_decode($this->curl_download('http://api.petfinder.com/shelter.getPets?format=json&key=' . $key . '&id=' . $petfinder_id . '&token=' . $token . '&sig=' . $request_sig));

		return $furkid_json;
	}

	function curl_download($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		$output = curl_exec($ch);
	    curl_close($ch);

		return $output;
	}
}
