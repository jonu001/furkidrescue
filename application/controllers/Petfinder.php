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
				log_message('INFO', 'cURL is installed.');
			}
			$pets = $this->getPets();

			if (isset($pets)) {
				//echo $pets->{'petfinder'}->{'pets'}->{'pet'}[13]->{'name'}->{'$t'};die();
				//var_dump($this->exists_in_object_array('hasShots', $pets->{'petfinder'}->{'pets'}->{'pet'}[13]->{'options'}->{'option'}));die();
				$total_pets = (int)$pets->{'petfinder'}->{'lastOffset'}->{'$t'};
				log_message('INFO', $total_pets . ' pets returned from the API.');
				$total_pets--;

				if ($total_pets > 0) {

					$this->Petfinder_model->empty_pet_table();
					
					for($x = 0; $x <= $total_pets; $x++) {
						$general_arr = [];
						$breed_arr = [];
						$photo_arr = [];

						$pet = $pets->{'petfinder'}->{'pets'}->{'pet'}[18];

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
								 		 'altered' => $this->exists_in_object_array('altered', $pet->{'options'})/*,
								 		 'shots' => $this->exists_in_object_array('hasShots', $pet->{'options'}->{'option'}),
								 		 'house_trained' => $this->exists_in_object_array('housetrained', $pet->{'options'}->{'option'}),
								 		 'special_needs' => $this->exists_in_object_array('specialNeeds', $pet->{'options'}->{'option'}),
								 		 'no_cats' => $this->exists_in_object_array('noCats', $pet->{'options'}->{'option'})*/
								];
						/*
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
						*/
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
			log_message('ERROR', $e->getMessage());
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

	function log_message($message_type, $message) 
	{
		try {
			$custom_debug_level = $this->config->item('custom_debug_level');
			if (($custom_debug_level === 'ALL') OR ($custom_debug_level === 'ERROR' AND $message_type === 'ERROR') OR ($message_type === 'JOB'))
			{				
				$fp = fopen($this->config->item('custom_log_path'), 'a');
				$dt = new DateTime("now");
				$dt->setTimestamp(time());
				$line   = '[' . $dt->format('Y-m-d H:i:s') . '] Pet Load:' . $message_type . ': ' . $message . "\n";
				fwrite($fp, $line);
				fclose($fp);
				
			} 	
		} catch (Exception $e) {
			//send_email('ERROR', $e->getMessage());
		}
	}

	function exists_in_object_array($needle, $haystack)
	{


		if (is_array($haystack))
		{
			foreach ($haystack as $row)
			{
				if ($row->{'$t'} === $needle)
				{

				}
			} 
		} else {
			if ($haystack->{'$t'} === $needle)
			{

			}
		}

		return $result;
	}

	function key_exists($needle, $haystack)
	{
		if (is_object($haystack)) {
			
		}
	}
}