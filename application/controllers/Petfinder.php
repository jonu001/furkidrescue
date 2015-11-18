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

			$total_pets = (int)$pets->{'petfinder'}->{'lastOffset'}->{'$t'};
			log_message('INFO', $total_pets . ' pets returned from the API.');
			$total_pets--;

			if ($total_pets > 0) {

				$this->Petfinder_model->empty_pet_table();
				
				for($x = 0; $x <= $total_pets; $x++) {
					$general_arr = [];
					$breed_arr = [];
					$photo_arr = [];

					$general_arr = ['name' => (property_exists ($pets->{'petfinder'}->{'pets'}->{'pet'}[$x]->{'name'}, '$t') ? 
													$pets->{'petfinder'}->{'pets'}->{'pet'}[$x]->{'name'}->{'$t'} : ''),
							 		 'description' => (property_exists ($pets->{'petfinder'}->{'pets'}->{'pet'}[$x]->{'description'}, '$t') ? 
							 		 				$pets->{'petfinder'}->{'pets'}->{'pet'}[$x]->{'description'}->{'$t'} : ''),
							 		 'sex' => (property_exists ($pets->{'petfinder'}->{'pets'}->{'pet'}[$x]->{'sex'}, '$t') ? 
							 		 				$pets->{'petfinder'}->{'pets'}->{'pet'}[$x]->{'sex'}->{'$t'} : ''),
							 		 'age' => (property_exists ($pets->{'petfinder'}->{'pets'}->{'pet'}[$x]->{'age'}, '$t') ? 
							 		 				$pets->{'petfinder'}->{'pets'}->{'pet'}[$x]->{'age'}->{'$t'} : ''),
							 		 'pet_id' => $pets->{'petfinder'}->{'pets'}->{'pet'}[$x]->{'id'}->{'$t'},
							 		 'type' => (property_exists ($pets->{'petfinder'}->{'pets'}->{'pet'}[$x]->{'animal'}, '$t') ? 
							 		 				$pets->{'petfinder'}->{'pets'}->{'pet'}[$x]->{'animal'}->{'$t'} : '')
							];

					$breed_count = count($pets->{'petfinder'}->{'pets'}->{'pet'}[$x]->{'breeds'}->{'breed'});
					if ($breed_count > 1) {
						$breed_count--;
						for($y = 0; $y <= $breed_count; $y++) {
							array_push($breed_arr, ['breed' => $pets->{'petfinder'}->{'pets'}->{'pet'}[$x]->{'breeds'}->{'breed'}[$y]->{'$t'},
												'pet_id' => $pets->{'petfinder'}->{'pets'}->{'pet'}[$x]->{'id'}->{'$t'}
												]);
						}

					} else {
						array_push($breed_arr, ['breed' => $pets->{'petfinder'}->{'pets'}->{'pet'}[$x]->{'breeds'}->{'breed'}->{'$t'},
											'pet_id' => $pets->{'petfinder'}->{'pets'}->{'pet'}[$x]->{'id'}->{'$t'}
										]);
					}

					
					$photo_count = count($pets->{'petfinder'}->{'pets'}->{'pet'}[$x]->{'media'}->{'photos'}->{'photo'});
					if ($photo_count > 1) {
						$photo_count--;
						for($z = 0; $z <= $photo_count; $z++) {
							array_push($photo_arr, ['photo' => $pets->{'petfinder'}->{'pets'}->{'pet'}[$x]->{'media'}->{'photos'}->{'photo'}[$z]->{'$t'},
											'pet_id' => $pets->{'petfinder'}->{'pets'}->{'pet'}[$x]->{'id'}->{'$t'}
											]);
						}
					} else {
							array_push($photo_arr, ['photo' => $pets->{'petfinder'}->{'pets'}->{'pet'}[$x]->{'media'}->{'photos'}->{'photo'}->{'$t'},
											'pet_id' => $pets->{'petfinder'}->{'pets'}->{'pet'}[$x]->{'id'}->{'$t'}
											]);
					}
					

					//var_dump($breed_arr);
					$pet = new Pet();
					$pet->general = $general_arr;
					$pet->breed = $breed_arr;
					$pet->photo = $photo_arr;

					//var_dump($breed_arr);
					$this->Petfinder_model->insert_pet($pet);

				}
				

			}

		} catch (Exception $e) {

		}
	}

	function getPets()
	{
		try {
			$petfinder_id = $this->config->item('shelter_id', 'petfinder_api');
			$url = $this->config->item('url', 'petfinder_api');
			$key = $this->config->item('key', 'petfinder_api');
			$secret = $this->config->item('secret', 'petfinder_api');
			$token_sig = md5($secret . 'format=json&key=' . $key);

			$token_json = json_decode($this->curl_download($url . 'auth.getToken?format=json&key=' . $key . '&sig=' . $token_sig));
			$token = $token_json->{'petfinder'}->{'auth'}->{'token'}->{'$t'};
			$request_sig = md5($secret . 'format=json&key=' . $key . '&id=' . $petfinder_id . '&token=' . $token);
			$furkid_json = json_decode($this->curl_download($url . 'shelter.getPets?format=json&key=' . $key . '&id=' . $petfinder_id . '&token=' . $token . '&sig=' . $request_sig));

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

}
