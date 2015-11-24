<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Adoptable_model extends CI_Model {

        public function __construct()
        {
            parent::__construct();
        }

        public function get_pet_general()
        {
            $this->db->select('*');
            $this->db->from('pet');
           return $this->db->get()->result_array();
        }

        public function get_pet_breeds()
        {
            $this->db->select('*');
            $this->db->from('pet_breed');
            $this->db->join('pet', 'pet_breed.pet_id = pet.pet_id');
            return $this->db->get()->result_array();
        }

        public function get_pet_photos()
        {
            $this->db->select('*');
            $this->db->from('pet_photo');
            $this->db->join('pet', 'pet_photo.pet_id = pet.pet_id');
            return $this->db->get()->result_array();
        }
}