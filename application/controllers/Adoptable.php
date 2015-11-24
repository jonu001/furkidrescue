<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Adoptable extends CI_Controller {

	public function index()
	{

		$this->load->model('Adoptable_model', '', TRUE);
		
		$data = ['pet_general' => $this->get_pet_general(),
				 'pet_breeds' => $this->get_pet_breeds(),
				 'pet_photos' => $this->get_pet_photos()];

		$this->load->view('inc/header');
		$this->load->view('adoptable', $data);
		$this->load->view('inc/footer');
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
