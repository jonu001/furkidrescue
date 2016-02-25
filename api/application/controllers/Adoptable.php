<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Adoptable extends REST_Controller {

	public function __construct()
   {
        parent::__construct();
        $this->load->model('Adoptable_model', '', TRUE);
   }

	public function index()
	{
		/*
		$data = ['pet_general' => $this->get_pet_general(),
				 'pet_breeds' => $this->get_pet_breeds(),
				 'pet_photos' => $this->get_pet_photos()];

		$this->load->view('inc/header');
		$this->load->view('adoptable', $data);
		$this->load->view('inc/footer');
		*/
	}

	public function pets_get() {
		$pets = $this->Adoptable_model->get_pet_general();

		if ($pets)
	    {
	        $this->response($pets, 200);
	    } else {
	        $this->response([], 404);
	    }
	}

	function get_pet_general() {
		return $this->Adoptable_model->get_pet_general();
	}

	function get_pet_breeds() {
		return $this->Adoptable_model->get_pet_breeds();
	}

	function get_pet_photos() {
		return $this->Adoptable_model->get_pet_photos();
	}
}
