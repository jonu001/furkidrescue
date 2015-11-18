<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Petfinder_model extends CI_Model {

        public $title;
        public $content;
        public $date;

        public function __construct()
        {
            parent::__construct();
        }

        public function insert_pet($pet)
        {
            $this->db->insert('pet', $pet->general);
            //var_dump($pet->breed);

            
            foreach($pet->breed as $breed_info)
            {
                //var_dump($breed_info);
                 $this->db->insert('pet_breed', $breed_info);
                /*
                foreach($breed_info as $omg) {
                 $this->db->insert('pet_breed', $omg);
                }*/
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