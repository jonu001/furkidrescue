<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Adoptable extends CI_Controller {

	public function index()
	{

		//$data = ['pets' => $this->getPets()];
		$pets = $this->getPets();
		$pet1 = ['name' => $pets->{'petfinder'}->{'pets'}->{'pet'}[0]->{'name'}->{'$t'},
				 'breed' => $pets->{'petfinder'}->{'pets'}->{'pet'}[0]->{'breeds'}->{'breed'}, //array
				 'description' => $pets->{'petfinder'}->{'pets'}->{'pet'}[0]->{'description'}->{'$t'},
				 'sex' => $pets->{'petfinder'}->{'pets'}->{'pet'}[0]->{'sex'}->{'$t'},
				 'age' => $pets->{'petfinder'}->{'pets'}->{'pet'}[0]->{'age'}->{'$t'},
				 'photos' => $pets->{'petfinder'}->{'pets'}->{'pet'}[0]->{'media'}->{'photos'}->{'photo'}, //array
				 'id' => $pets->{'petfinder'}->{'pets'}->{'pet'}[0]->{'id'}->{'$t'},
				 'type' => $pets->{'petfinder'}->{'pets'}->{'pet'}[0]->{'animal'}->{'$t'}
				];
		print_r($pet1);
		$this->load->view('inc/header');
		$this->load->view('adoptable', $data);
		$this->load->view('inc/footer');
	}

	function getPets()
	{
		$petfinder_id = 'PA660'; // fur kid rescue
		$key = $this->config->item('key', 'petfinder_api');
		$secret = $this->config->item('secret', 'petfinder_api');
		$token_sig = md5($secret . 'format=json&key=' . $key);

		$token_json = json_decode($this->curl_download('http://api.petfinder.com/auth.getToken?format=json&key=' . $key . '&sig=' . $token_sig));
		$token = $token_json->{'petfinder'}->{'auth'}->{'token'}->{'$t'};
		$request_sig = md5($secret . 'format=json&key=' . $key . '&id=' . $petfinder_id . '&token=' . $token);
		$furkid_json = json_decode($this->curl_download('http://api.petfinder.com/shelter.getPets?format=json&key=' . $key . '&id=' . $petfinder_id . '&token=' . $token . '&sig=' . $request_sig));

		return $furkid_json;
	}
}
