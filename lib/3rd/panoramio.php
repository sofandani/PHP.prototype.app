<?php
/**
 * Simple class for retreiving images from the Panoramio API
 * 
 * @package Panoramio Wrapper Class
 * @author Anthony Mills
 * @copyright 2012 Anthony Mills ( http://anthony-mills.com )
 * @license GPL V3.0
 * @version 0.1
 */
 
 class panoramioAPI 
 {
	// Supplied Cordinates to search near for images
	protected $_requiredLatitude;
	protected $_requiredLongitude;	
	
	// The outer limits of the box we would like to search for images within
	protected $_requiredMinLatitude = 0;
	protected $_requiredMinLongitude = 0;
	protected $_requiredMaxLatitude = 0;
	protected $_requiredMaxLongitude = 0;
	
	// The distance in kilometers from the position you would like to search for images
	protected $_locationDistance = 20;
		
	// The default type of Panoramio image set to retrieve
	protected $_panoramioSet = 'public';
	
	// The size for the return images
	protected $_panoramioImageSize = 'medium';
	
	// Ordering style of the images
	protected $_panoramioOrdering = 'upload_date';

	// Specifics for communication with the actual URL itself
	protected $_requestUserAgent = 'info@mypanoramiobot.com';
	protected $_requestHeaders = array('Panoramio-Client-Version: 0.1');
	protected $_apiUrl = 'http://www.panoramio.com/map/get_panoramas.php';

	protected $_calculateBox;
	protected $_cache_dir;


	public function __construct($args=false)
	{
		$this->_args = is_array($args) ? $args : null;

 		$this->_calculateBox = isset($args['calc_box']) ? $args['calc_box'] : true;
 		$this->_panoramioImageNumber = isset($args['img_num']) ? $args['img_num'] : 20;
 		$this->_panoramioStartingImage = isset($args['start_img']) ? $args['start_img'] : 0;
 		$this->_cache_dir = 'json';

		if(method_exists('GoogleGeocode','get_geocode') == false)
			require_once(dirname(__FILE__).'/geocode.php');

		try
		{
			$city = isset($args['city']) ? $args['city'] : 'Kuningan, Jawa Barat';
			$GoogleGeocode = new GoogleGeocode(array('city'=>$city,'typedata'=>'json'));
			$get_geocode = $GoogleGeocode->get_geocode();

			$this->_requiredLatitude = $get_geocode->results[0]->geometry->location->lat;
			$this->_requiredLongitude = $get_geocode->results[0]->geometry->location->lng;
		}
		catch(GoogleGeocodeException $e)
		{
			throw new PanoramioException($e->getMessage());
		}
	}

	/**
	 * Set the location via longitude and latitude of where you would like to get images near
	 * 
	 * @param string $placeLatitude
	 * @param string $placeLongitude
	 */
	public function setRequiredLocation($placeLatitude, $placeLongitude,$locationDistance)
	{
		$this->_requiredLatitude = $placeLatitude;
		$this->_requiredLongitude = $placeLongitude;
		$this->_locationDistance = $locationDistance;
	}


	/**
	* Set the location box via min/max longitude and latitude of where you would like to get images.
	*
	* @param string $requiredMinLatitude
	* @param string $requiredMaxLatitude
	* @param string $requiredMinLongitude
	* @param string $requiredMaxLongitude
	*/
	public function setBoxLocation($requiredMinLatitude, $requiredMaxLatitude, $requiredMinLongitude, $requiredMaxLongitude)
	{
		// The outer limits of the box we would like to search for images within
		$this->_requiredMinLatitude = $requiredMinLatitude;
		$this->_requiredMaxLatitude = $requiredMaxLatitude;
		$this->_requiredMinLongitude = $requiredMinLongitude;
		$this->_requiredMaxLongitude = $requiredMaxLongitude;
	}


	/**
	 * Set the ordering of images returned from Pamoramio, class default is upload_date but 
	 * can also be set to "popularity"
	 * 
	 * @param string $imageOrder
	 */
	public function orderImages($imageOrder) 
	{
		$this->_panoramioOrdering = $imageOrder;
	}

	
	/**
	 * Set the tyep of set you would like to retrieve this can be either:
	 * 
	 * - public (popular photos)
	 * - full (all photos)
	 * - the user ID of a panoramio user whose photos you would like returned
	 * 
	 * @param string $panoramioSet
	 */
	public function setPanoramioSet($panoramioSet)
	{
		$this->_panoramioSet = $panoramioSet;	
	}

	
	/**
	 * Set a size for the images returned from the Panoramio API
	 * Valid size API options are:
	 * 		original
	 * 		thumbnail
	 * 		mini square
	 * 		square
	 * 		small
	 * 		medium (default)
	 * 
	 * @param string $panoramioSize
	 */	
	 public function setPanoramioSize($panoramioSize)
	 {
	 	$this->_panoramioImageSize = $panoramioSize;
	 }

	
	/**
	 * Get a set of images from the Panoramio API
	 * 
	 * @param int $imageNumber
	 * @return object 
	 */	
	 public function getPanoramioImages()
	 {
	 	$calculateBox = $this->_calculateBox;
		if($calculateBox)
		{
			$this->_calculateBoundingBox();
		}

		$apiResponse = $this->_CacheAPI();
		
		if( $apiResponse == null )
		{
			throw new PanoramioException('Failed Request API');
		}
		else
		{
			return json_decode($apiResponse);
		}
	 }


	/**
	 * _CacheAPI()
	 * Fungsi proteksi untuk proses pembuatan file cache dalam format json
 	 * @return JSON file dari file_get_contents
	 */
	protected function _CacheAPI()
	{
		// Pengaturan untuk cache file
		$data = $this->_args;
		$cache_dir = isset($data['cache_dir']) ? $data['cache_dir'] : $this->_cache_dir;
		$city = isset($data['city']) ? $data['city'] : 'Kuningan';

		$basedir = defined('BASEDIR') ? BASEDIR : dirname(__FILE__).'/../app/panoramio/location';

		$stored = $basedir.'/'.$cache_dir.'/panoramio-'.md5($city).'.json';
		$expire_cache = isset($data['expire_cache']) ? $data['expire_cache'] : strtotime('+1 Hour');

		// Hapus cache jika melampaui batas expire
		if( file_exists($stored) AND ( filemtime($stored) < strtotime('now') ) )
		{
			unlink($stored);
		}

		// Buat file cache baru jika file tidak ditemukan di direktori
		if( !file_exists($stored) )
		{
			// Menggunakan _ServeiceWeather() untuk mengambil data API
			$pr = $this->_processRequest();

			// Jika hasil data API false maka di return null
			if($pr == false)
			{
				return null;
			}
			else
			{
				// Buat file cache & rubah meta time nya
				@file_put_contents($stored, $pr);
				touch($stored, $expire_cache);

				// Definisikan nilai $data
				return $pr;
			}
		}
		else
		{
			// Nilai data dari lokal file cache
			$data = @file_get_contents($stored);
			return $data;
		}
	}


	/** 
	 * Calculate the bounding box for a location via its latitude, longitude
	 */
	 protected function _calculateBoundingBox()
	 {
		$minLocation = $this->_calculateNewPosition($this->_requiredLatitude, $this->_requiredLongitude, 225);
		$this->_requiredMinLatitude = $minLocation['latitude'];
		$this->_requiredMinLongitude = $minLocation['longitude'];
		 
		$maxLocation = $this->_calculateNewPosition($this->_requiredLatitude, $this->_requiredLongitude, 45);
		$this->_requiredMaxLatitude = $maxLocation['latitude'];
		$this->_requiredMaxLongitude = $maxLocation['longitude'];
	 }

	 
	 /**
	  * Calculate the position of a new location given a longitude and latitude and a bearing
	  * 
	  * @param int $placeLatitude
	  * @param int $placeLongitude
	  * @param int $directionBearing
	  * 
	  * @return array $newLocation
	  */
	 protected function _calculateNewPosition($placeLatitude, $placeLongitude, $directionBearing)
	 {
	 	$earthRadius = 6371; // Radius of the earth in kilometers
		$newLocation = array();
		$newLocation['latitude'] = rad2deg(asin(sin(deg2rad($placeLatitude)) * cos($this->_locationDistance / $earthRadius) + cos(deg2rad($placeLatitude)) * sin($this->_locationDistance / $earthRadius) * cos(deg2rad($directionBearing))));
		$newLocation['longitude'] = rad2deg(deg2rad($placeLongitude) + atan2(sin(deg2rad($directionBearing)) * sin($this->_locationDistance / $earthRadius) * cos(deg2rad($placeLatitude)), cos($this->_locationDistance / $earthRadius) - sin(deg2rad($placeLatitude)) * sin(deg2rad($newLocation['latitude']))));	 
		
		return 	$newLocation;
	 }

	 
	 /**
	  * Assemble the request data in preperation for passing to the API
	  * 
	  * @return string $APIendpoint
	  */
	  protected function _APIendpoint()
	  {
		$APIendpoint = $this->_apiUrl . '?set=' . $this->_panoramioSet .
						'&from=' . $this->_panoramioStartingImage .
						'&to=' . ($this->_panoramioStartingImage + $this->_panoramioImageNumber) .
						'&minx=' . $this->_requiredMinLongitude  . '&miny=' . $this->_requiredMinLatitude. 
						'&maxx=' . $this->_requiredMaxLongitude . '&maxy=' . $this->_requiredMaxLatitude . 
						'&size=' . $this->_panoramioImageSize . '&order=' . $this->_panoramioOrdering;
		
		return $APIendpoint;
	  }

	  
	 /**
	  * Send a formatted string of data as a GET to the API and collect the response
	  * 
	  * @param string $apiData
	  * @return array $apiResponse
	  */
	 protected function _processRequest()
	 {
	 	if(method_exists('cURLs','access_curl'))
	 	{
			$APIendpoint = $this->_APIendpoint();

			$cURLs = new cURLs(array('url'=>$APIendpoint,'type'=>'data'));
			$r = $cURLs->access_curl();
			
			if( $r == false )
			{
				return null;
			}
			else
			{
				return $r;
			}
		}
		else{
			return null;
		}	
	 }
 }


/**
 * new Throw PanoramioException
 */
class PanoramioException extends Exception{}