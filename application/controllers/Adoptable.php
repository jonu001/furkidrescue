<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Adoptable extends CI_Controller {

	public function index()
	{


		$data = ['pets' => $this->get_pets()];

		$this->load->view('inc/header');
		$this->load->view('adoptable', $data);
		$this->load->view('inc/footer');
	}

	function get_pets() {
		$this->load->model('Adoptable_model', '', TRUE);
		return $this->Adoptable_model->get_pets();

	}
}
