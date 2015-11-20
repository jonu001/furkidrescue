<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Adoptable_model extends CI_Model {

        public function __construct()
        {
            parent::__construct();
        }

        public function get_pets()
        {
            $this->db->select('*');
            $this->db->from('pet');
            $this->db->join('pet_breed', 'pet.pet_id = pet_breed.pet_id', 'left outer');
            $this->db->join('pet_photo', 'pet.pet_id = pet_photo.pet_id', 'left outer');
            return $this->db->get();
        }

}