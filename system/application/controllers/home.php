	<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
	
	/**
	 * undocumented class
	 *
	 * @package default
	 * @author author
	 **/
	class Home extends Controller {
	
		var $from_city;
		var $from_state;
		var $to_city;
		var $to_state;
		var $radius;
		var $month;
		var $day;
		var $year;
		
		var $airportInfo = array();
		var $busInfo = array();
		var $drivingPrice = NULL;
	
		/**
		 * classname constructor
		 *
		 * @return void
		 * @author author
		 **/
		function Home()
		{
			parent::Controller();
			$this->load->library('form');
			$this->load->library('template');
			log_message('debug', 'classname Controller Initialized');
			// constructor code
		}
	
		/**
		 * index function
		 *
		 * @return void
		 * @author author
		 **/
		function index()
		{
			$this->form
			->open('')
			->text('location', 'Your Location', 'required|trim')
			->text('to_location', 'Where do you want to go?', 'required|trim')
			->text('radius', 'Radius', 'required|trim')
			->text('month', 'Month of Depature', 'required|trim')
			->text('day', 'Day of Depature', 'required|trim')
			->submit('Go!')
			->reset();
			$data['form'] = $this->form->get();

			if($this->form->valid){
				//validation passed
				$vars = $this->form->get_post();
				$location = $vars['location'];
				$to_location = $vars['to_location'];
				$this->radius = trim($vars['radius']);
				$this->month = trim($vars['month']);
				$this->day = trim($vars['day']);
				$d = explode(",", $location);
				$t = explode(",", $to_location);
				$this->from_city = ucfirst(trim($d[0]));
				$this->from_state = strtoupper(trim($d[1]));
				$this->to_city = ucfirst(trim($t[0]));
				$this->to_state = strtoupper(trim($t[1]));
				
				
				$this->_go();
			} else {
				$data['errors'] = $this->form->errors;
			}
			
			
			$data['airportInfo'] = $this->airportInfo;
			$data['busInfo'] = $this->busInfo;
			$data['drivingInfo'] = array('to'=>$this->to_city, 'from'=>$this->from_city, 'price'=>$this->drivingPrice);
			
			
			// write to template...
			$this->template->write_view('header', 'header');
			$this->template->write_view('content', 'content', $data);
			$this->template->write_view('footer', 'footer');
			$this->template->render();
		}
		
		
		function _go(){

			// airport			
			$from_airports = $this->_getAirportsByCity( $this->from_city, $this->from_state );
			$to_airports = $this->_getAirportsByCity( $this->to_city, $this->to_state );
			$combinationAirports = $this->_getAirportCombinations($from_airports, $to_airports);
			
			if($this->_getAirportCombinations($from_airports, $to_airports) === false){
				//$this->airportInfo = array();
			}
			
			
			$from_buses = $this->_getBusesByCity( $this->from_city, $this->from_state );
			$to_buses = $this->_getBusesByCity( $this->to_city, $this->to_state );
			if($this->_getBusCombinations( $from_buses, $to_buses ) === false){
				///dle error
			//	$this->busInfo = array();
			}
		if($this->_getDriving() === false){
			//oops	
			//$this->drivingPrice = NULL;
		}

		
	}
		

		function _getBusesByCity( $c, $s ){
			list($lat, $lng) = $this->_getCoordinates($c, $s);
			$buses = new Greyhound();
			$buses->query("SELECT *, ( 3959 * acos( cos( radians(". $this->db->escape_str($lat) . ") ) * cos( radians( lat ) ) * cos( radians(lng) - radians(". $this->db->escape_str($lng) .") ) + sin( radians(". $this->db->escape_str($lat) . ") ) * sin( radians( lat ) ) ) ) AS
	distance FROM (`bus_stations`) HAVING `distance` < " . $this->db->escape_str($this->radius) . " LIMIT 0,3");

			return $buses->all;
		}
		
		function _getDriving(){
			
			$from = $this->from_city . "," . $this->from_state;
			$to = $this->to_city . "," . $this->to_state;
			$drive_cache = new DriveCache();
			$drive_cache->where('from', $from)->where('to', $to)->get();
			if(!$drive_cache->exists()){
			
				$this->load->library('domparser');

				$html = $this->domparser->file_get_html('http://www.travelmath.com/fuel-cost/from/' . $this->from_city .',+'. $this->from_state .'/to/'. $this->to_city .',+'. $this->to_state);


				$article = $html->find('h3[id=costofdriving]');
				if(count($article) == 0){
					return false;
				}
				$gas_prices = $article[0]->plaintext;
				preg_match("/(.*?)\s+/", $gas_prices, $matches);
				$this->drivingPrice = $matches[1];
			
				$b = new DriveCache();
				$b->from = $from;
				$b->to = $to;
				$b->month = $this->month;
				$b->day= $this->day;
				$b->price = $matches[1];
				$b->save();
			} else {
				$this->drivingPrice = $drive_cache->price;
				
			}
			
		}
		
		function _getFlightInfo( $from_code, $to_code ){
		 //implement
		 
		 $foc = $from_code;
		 $toc = $to_code;
		 
		 $flight_cache = new AirportCache();
		$flight_cache->where('from', $foc)->where('to', $toc)->get();
			
			
		 if(!$flight_cache->exists()){
		 
			 $from_code = escapeshellarg($from_code);
			 $to_code = escapeshellarg($to_code);
			 $command = "ruby scripts/scrape_kayak.rb f $from_code $to_code $this->month/$this->day/2010";
			 $scrape_output = `$command`;
		 
			 //xml crap here
			try { 
				$xml = @new SimpleXMLElement( $scrape_output );
			} catch (Exception $e){
				return false;
			}
			if($xml === false) return false;
			if(count($xml->trips->trip) == 0) return false;
			$price = $xml->trips->trip[0]->price[0];
			$airline = $xml->trips->trip[0]->legs->leg[0]->segment->airline[0];
			$flight = $xml->trips->trip[0]->legs->leg[0]->segment->flight[0];
			
			
			$b = new AirportCache();
			$b->from = $foc;
			$b->to = $toc;
			$b->month = $this->month;
			$b->day= $this->day;
			$b->price = $price;
			$b->flight = $flight;
			//$b->airline = $airline;
			$b->save();
					
			$retArray = array(
								'price'=>$price,
								'flight_number'=>$flight
								);

			return $retArray;
					
		} else {
		
			$retArray = array(
								'price'=>$flight_cache->price,
								'flight_number'=>$flight_cache->flight
								);
							
			return $retArray;
		}
	}
		
		function _getBusPrice( $from_city, $from_state, $to_city, $to_state ){
		
			$from_city = escapeshellarg($from_city);
			$from_state = escapeshellarg($from_state);
			$to_city = escapeshellarg($to_city);
			$to_state = escapeshellarg($to_state);

			$from = $this->from_city . "," . $this->from_state;
			$to = $this->to_city . "," . $this->to_state;
			$bus_cache = new BusCache();
			$bus_cache->where('from', $from)->where('to', $to)->get();
			
			if(!$bus_cache->exists()){
				$scrape_output = `python scripts/scrape_greyhound.py $from_city $from_state $to_city $to_state $this->month $this->day`;
			
				if(strpos($scrape_output, "bad") !== false || strpos($scrape_output, "Traceback") !== false){
					return false;
				} else {
				
					$b = new BusCache();
					$b->from = $from;
					$b->to = $to;
					$b->month = $this->month;
					$b->day= $this->day;
					$b->price = $scrape_output;
					$b->save();
					return $scrape_output;
				}
			} else {
				return $bus_cache->price;
			}
				
				
			}
		
		function _getAirportCombinations($from, $to){
		
		$found = false;
			/** build airport code array */
			$retArray = array();
			$displayInfo = array();
			foreach($from as $f){
			if($found) break;
				foreach($to as $t){
				
				$flight_cache = new AirportCache();
				$flight_cache->where('from', $f->code)->where('to', $t->code)->get();


					if($flight_cache->exists()){
					
						$found = true;
						$flightInfo = array('price'=>$flight_cache->price, 'flight_number'=>$flight_cache->flight);
						$displayInfo[] = array( 
													'from'=>$f->name,
													'to'=>$t->name,
													'data'=> $flightInfo
													);
						break;
					
					}
						if($f->code != $t->code){
							$f_code = $f->code;
							$retArray[] = array($f_code => $t->code);
							// get kayak information
							$flightInfo = $this->_getFlightInfo($f->code, $t->code);
							if($flightInfo === false) return false;
							$displayInfo[] = array( 
													'from'=>$f->name,
													'to'=>$t->name,
													'data'=> $flightInfo
													);
							
					//	$flights[] $this->_getFlightInfo( $
					
					
						}
						
					
				}
			}
			
			$this->airportInfo = $displayInfo;
		}
		
		function _getAirportsByCity( $c, $s ){
			list($lat, $lng) = $this->_getCoordinates($c, $s);
			$airports = new Airport();
			$airports->query("SELECT *, ( 3959 * acos( cos( radians(". $this->db->escape_str($lat) . ") ) * cos( radians( lat ) ) * cos( radians(lng) - radians(". $this->db->escape_str($lng) .") ) + sin( radians(". $this->db->escape_str($lat) . ") ) * sin( radians( lat ) ) ) ) AS
	distance FROM (`airports`) HAVING `distance` < " . $this->db->escape_str($this->radius) . " LIMIT 0,3");

			
			return $airports->all;
		}
		
	
		
		function _getCoordinates($city, $state){
		
			$coordinates = new Coordinate();
			$coordinates->where('city' , $city)->where('state', $state)->get();
			if(!$coordinates->exists()){
				
				$data = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=" . urlencode($city) . ",". urlencode($state) ."&sensor=false");
				$json_array = json_decode($data, true);
				$lat = $json_array['results'][0]['geometry']['location']['lat'];
				$long = $json_array['results'][0]['geometry']['location']['lng'];
				$c = new Coordinate();
				$c->city = $city;
				$c->state = $state;
				$c->lat = $lat;
				$c->lng = $long;
				$c->save();
			} else {
				$lat = $coordinates->lat;
				$long = $coordinates->lng;
			}
			return array($lat, $long);
		}
		
		
		
		function _getBusCombinations($from, $to){

			$found = false;
			/** build airport code array */
			$retArray = array();
			$displayInfo = array();
			foreach($from as $f){
				if($found) break;
				
				foreach($to as $t){
					$from = $this->from_city . "," . $this->from_state;
			$to = $this->to_city . "," . $this->to_state;
			$bus_cache = new BusCache();
			$bus_cache->where('from', $from)->where('to', $to)->get();
					if($bus_cache->exists()){
						$found = true;
						$displayInfo[] = array(
												'from'=> array(
																'name'=>$f->name,
																'city'=>$f->city,
																'state'=>$f->state
																),

												'to'=> array(
																'name'=>$t->name,
																'city'=>$t->city,
																'state'=>$t->state
															),
												'price'=>$bus_cache->price
												);
							break;
						}
					if($f->name != $t->name){
						$retArray[] = array($f->code => $t->code);
						// get greyhound information

						
						$bus_price = $this->_getBusPrice( $this->from_city, $this->from_state, $this->to_city, $this->to_state);
						
						if($bus_price === false){
							return false;
						}
						
						$displayInfo[] = array(
												'from'=> array(
																'name'=>$f->name,
																'city'=>$f->city,
																'state'=>$f->state
																),

												'to'=> array(
																'name'=>$t->name,
																'city'=>$t->city,
																'state'=>$t->state
															),
												'price'=>$bus_price
												);


					}
				}
			}
			
			$this->busInfo = $displayInfo;
		}
		
		
	
	}
	
	/* End of file home.php */
	/* Location: ./system/application/controllers/classname.php */
