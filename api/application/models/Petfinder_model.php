<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Petfinder_model extends CI_Model {

        public function __construct()
        {
            parent::__construct();
        }

        public function insert_pet($pet)
        {
            $this->db->insert('pet', $pet->general);

            foreach($pet->breed as $breed_info)
            {
                 $this->db->insert('pet_breed', $breed_info);
            }
            
            foreach($pet->photo as $photo_info)
            {
                $this->db->insert('pet_photo', $photo_info);
            }
            
        }

        public function empty_pet_table()
        {
            $this->db->empty_table('pet');
            $this->db->empty_table('pet_breed');
            $this->db->empty_table('pet_photo');
        }
}