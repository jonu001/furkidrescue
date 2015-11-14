<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Adoptable extends CI_Controller {

	public function index()
	{
		$this->load->model('Petfinder', '', TRUE););

		try {


			$mysqli = new mysqli($doc_db_host, $doc_db_username, $doc_db_password, $doc_db_name);
			if ( ! function_exists('curl_init'))
			{
				throw new Exception('cURL is not installed.');
			}
			else 
			{
				log_message('INFO', 'cURL is installed.');
			}

			$pets = $this->getPets();
			$total_pets = (int)$pets->{"petfinder"}->{"lastOffset"}->{"$t"};

			log_message('INFO', $total_pets . ' pets returned from the API.');

			if ($mysqli->connect_errno) 
			{
			    throw new Exception('Failed to connect to MySQL: (' . $mysqli->connect_errno . ') '  . $mysqli->connect_error);
			}
			else 
			{
				log_message('INFO', 'Connected to MySQL.');
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
