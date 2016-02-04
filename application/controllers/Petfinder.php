<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Petfinder extends CI_Controller {

	public function index()
	{
		$this->load->model('Petfinder_model', '', TRUE);
		$this->load->library('Pet');

		try {

			if ( ! function_exists('curl_init'))
			{
				throw new Exception('cURL is not installed.');
			}
			else 
			{
				log_message('info', 'cURL is installed.');
			}
			$pets = $this->getPets();
			//var_dump($pets);die();
			if (isset($pets)) {
	;			//echo $pets->{'petfinder'}->{'pets'}->{'pet'}[13]->{'name'}->{'$t'};die();
				//var_dump($this->exists_in_object_array('hasShots', $pets->{'petfinder'}->{'pets'}->{'pet'}[13]->{'options'}->{'option'}));die();
				$total_pets = (int)$pets->{'petfinder'}->{'lastOffset'}->{'$t'};
				log_message('info', $total_pets . ' pets returned from the API.');
				$total_pets--;

				if ($total_pets > 0) {

					$this->Petfinder_model->empty_pet_table();
					
					for($x = 0; $x <= $total_pets; $x++) {
						$general_arr = [];
						$breed_arr = [];
						$photo_arr = [];
						$pet = $pets->{'petfinder'}->{'pets'}->{'pet'}[$x];

						$general_arr = [
								 		 'pet_id' => $pet->{'id'}->{'$t'},
								 		 'name' => (property_exists ($pet->{'name'}, '$t') ? 
														$pet->{'name'}->{'$t'} : ''),
								 		 'description' => (property_exists ($pet->{'description'}, '$t') ? 
								 		 				$pet->{'description'}->{'$t'} : ''),
								 		 'sex' => (property_exists ($pet->{'sex'}, '$t') ? 
								 		 				$pet->{'sex'}->{'$t'} : ''),
								 		 'age' => (property_exists ($pet->{'age'}, '$t') ? 
								 		 				$pet->{'age'}->{'$t'} : ''),
								 		 'type' => (property_exists ($pet->{'animal'}, '$t') ? 
								 		 				$pet->{'animal'}->{'$t'} : ''),
								 		 'size' => (property_exists ($pet->{'size'}, '$t') ? 
								 		 				$pet->{'size'}->{'$t'} : ''),
								 		 'altered' => $this->pet_option_exists('altered', $pet->{'options'}),
								 		 'shots' => $this->pet_option_exists('hasShots', $pet->{'options'}),
								 		 'house_trained' => $this->pet_option_exists('housetrained', $pet->{'options'}),
								 		 'special_needs' => $this->pet_option_exists('specialNeeds', $pet->{'options'}),
								 		 'no_cats' => $this->pet_option_exists('noCats', $pet->{'options'})
								];
						
						$breed_count = count($pet->{'breeds'}->{'breed'});
						if ($breed_count > 1) {
							$breed_count--;
							for($y = 0; $y <= $breed_count; $y++) {
								array_push($breed_arr, ['breed' => $pet->{'breeds'}->{'breed'}[$y]->{'$t'},
													'pet_id' => $pet->{'id'}->{'$t'}
													]);
							}

						} else {
							array_push($breed_arr, ['breed' => $pet->{'breeds'}->{'breed'}->{'$t'},
												'pet_id' => $pet->{'id'}->{'$t'}
											]);
						}

						
						$photo_count = count($pet->{'media'}->{'photos'}->{'photo'});
						if ($photo_count > 1) {
							$photo_count--;
							for($z = 0; $z <= $photo_count; $z++) {
								array_push($photo_arr, ['photo' => $pet->{'media'}->{'photos'}->{'photo'}[$z]->{'$t'},
												'pet_id' => $pet->{'id'}->{'$t'}
												]);
							}
						} else {
							array_push($photo_arr, ['photo' => $pet->{'media'}->{'photos'}->{'photo'}->{'$t'},
												'pet_id' => $pet->{'id'}->{'$t'}
												]);
						}
						
						$pet = new Pet();
						$pet->general = $general_arr;
						$pet->breed = $breed_arr;
						$pet->photo = $photo_arr;

						$this->Petfinder_model->insert_pet($pet);

					}
				}
			} else {
				echo 'Petfinder cURL Issue';
			}

		} catch (Exception $e) {
			log_message('error', $e->getMessage());
		}
	}

	function getPets()
	{
		try {
			$petfinder_id = $this->config->item('shelter_id', 'petfinder_api');
			$url = $this->config->item('url', 'petfinder_api');
			$key = $this->config->item('key', 'petfinder_api');
			/*
			$secret = $this->config->item('secret', 'petfinder_api');
			$token_sig = md5($secret . 'format=json&key=' . $key);

			$token_json = json_decode($this->curl_download($url . 'auth.getToken?format=json&key=' . $key . '&sig=' . $token_sig));
			$token = $token_json->{'petfinder'}->{'auth'}->{'token'}->{'$t'};
			$request_sig = md5($secret . 'format=json&key=' . $key . '&id=' . $petfinder_id . '&token=' . $token);
			*/
			$furkid_json = json_decode($this->curl_download($url . 'shelter.getPets?format=json&key=' . $key . '&id=' . $petfinder_id));

		return $furkid_json;

		} catch (Exception $e) {
			throw $e;
		}
	}

	function curl_download($url)
	{
		try {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
			curl_setopt($ch, CURLOPT_TIMEOUT, 180);
			$output = curl_exec($ch);

			if ($errno = curl_errno($ch)) 
			{
				curl_close($ch);
				$error_message = curl_strerror($errno);
			    throw new Exception("cURL error ({$errno}): {$error_message}");
			}
		    curl_close($ch);

			return $output;
		} catch (Exception $e) {
			throw $e;
		}
	}
/*
	array(1) {
	  ["option"]=>
	  array(3) {
	    [0]=>
	    object(stdClass)#776 (1) {
	      ["$t"]=>
	      string(8) "hasShots"
	    }
	    [1]=>
	    object(stdClass)#777 (1) {
	      ["$t"]=>
	      string(7) "altered"
	    }
	    [2]=>
	    object(stdClass)#778 (1) {
	      ["$t"]=>
	      string(12) "housetrained"
	    }
	  }
	}
*/
//array(1) { ["option"]=> array(3) { [0]=> object(stdClass)#776 (1) { ["$t"]=> string(8) "hasShots" } [1]=> object(stdClass)#777 (1) { ["$t"]=> string(7) "altered" } [2]=> object(stdClass)#778 (1) { ["$t"]=> string(12) "housetrained" } } }

// array(1) { ["option"]=> object(stdClass)#859 (1) { ["$t"]=> string(8) "hasShots" } }
	function pet_option_exists($needle, $haystack)
	{
		$return_val = FALSE;
		$object_array = get_object_vars($haystack);
		
		if (count($object_array) !== 0) {

			if (key($object_array) === 'option') {

				foreach($object_array as $i => $values) {
					if (count($values) > 1) {
						foreach($values as $key => $value) {
							if ($value->{'$t'}  === $needle) {
								$return_val = TRUE;
							}
						}
					} else {
						if ($values->{'$t'}  === $needle) {
							$return_val = TRUE;
						}
					}
				}
			}
		}
		return $return_val;
	}
}