<?php if ( !defined('BASEPATH')) header('Location:/404');
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
	protected static $_requiredLatitude;
	protected static $_requiredLongitude;

	protected static $_panoramioImageNumber;
	protected static $_panoramioStartingImage;
	
	// The outer limits of the box we would like to search for images within
	protected static $_requiredMinLatitude = 0;
	protected static $_requiredMinLongitude = 0;
	protected static $_requiredMaxLatitude = 0;
	protected static $_requiredMaxLongitude = 0;
	
	// The distance in kilometers from the position you would like to search for images
	protected static $_locationDistance = 20;
		
	// The default type of Panoramio image set to retrieve
	protected static $_panoramioSet = 'public';
	
	// The size for the return images
	protected static $_panoramioImageSize = 'medium';
	
	// Ordering style of the images
	protected static $_panoramioOrdering = 'upload_date';

	// Specifics for communication with the actual URL itself
	protected static $_requestUserAgent = 'info@mypanoramiobot.com';
	protected static $_requestHeaders = array('Panoramio-Client-Version: 0.1');
	protected static $_apiUrl = 'http://www.panoramio.com/map/get_panoramas.php';

	protected static $_calculateBox;
	protected static $_cache_dir;

	protected static $_args;


	function __construct()
	{
		if(method_exists('GoogleGeocode','get_geocode') == false)
			require_once(dirname(__FILE__).'/geocode.php');

		try
		{
			$city = isset($args['city']) ? $args['city'] : 'Kuningan, Jawa Barat';
			$GoogleGeocode = new GoogleGeocode();
			$get_geocode = $GoogleGeocode->get_geocode(array('city'=>$city,'typedata'=>'json'));

			$this->_requiredLatitude = $get_geocode->results[0]->geometry->location->lat;
			$this->_requiredLongitude = $get_geocode->results[0]->geometry->location->lng;
		}
		catch(GoogleGeocodeException $e)
		{
			throw new PanoramioException($e->getMessage());
		}
	}


	/**
	 * Get a set of images from the Panoramio API
	 * 
	 * @param int $imageNumber
	 * @return object 
	 */	
	 public static function getPanoramioImages($args=false)
	 {
		self::$_args = is_array($args) ? $args : null;

		try
		{
			$cache_dir = isset($args['cache_dir']) ? $args['cache_dir'] : self::$_cache_dir;
			
			$city = isset($args['city']) ? $args['city'] : 'Kuningan';

			$type_save = isset($args['type_save']) ? $args['type_save'] : 'database';

			$cache_table = isset($args['cache_table']) ? $args['cache_table'] : 'app_cache';

			$serialize = isset($args['serialize']) ? $args['serialize'] : true;

			$basedir = defined('BASEDIR') ? BASEDIR : dirname(__FILE__).'/../app/panoramio/json';

			$typedata = isset($args['typedata']) ? $args['typedata'] : 'json';

			$expire_cache = isset($args['expire_cache']) ? $args['expire_cache'] : strtotime('+1 Hour');

			// Cache Handler
			$cache = CacheHandler::save(array('method'=>array('panoramioAPI','_processRequest'),
											  'data'=>$args,
											  'cache_expire'=>$expire_cache,
											  'cache_prefix'=>'panoramio',
											  'cache_id'=>$city,
											  'cache_dir'=>$cache_dir,
											  'type_save'=>$type_save,
											  'cache_table'=>$cache_table,
											  'serialize'=>$serialize
											  )
										);

			$data = is_object($cache) ? $cache : json_decode($cache);
			return $data;
		}
		catch(CacheHandlerException $e)
		{
			throw new PanoramioException($e->getMessage().' from Panoramio');
		}
	 }


	/**
	 * Set the location via longitude and latitude of where you would like to get images near
	 * 
	 * @param string $placeLatitude
	 * @param string $placeLongitude
	 */
	public static function setRequiredLocation($placeLatitude, $placeLongitude,$locationDistance)
	{
		self::$_requiredLatitude = $placeLatitude;
		self::$_requiredLongitude = $placeLongitude;
		self::$_locationDistance = $locationDistance;
	}


	/**
	* Set the location box via min/max longitude and latitude of where you would like to get images.
	*
	* @param string $requiredMinLatitude
	* @param string $requiredMaxLatitude
	* @param string $requiredMinLongitude
	* @param string $requiredMaxLongitude
	*/
	public static function setBoxLocation($requiredMinLatitude, $requiredMaxLatitude, $requiredMinLongitude, $requiredMaxLongitude)
	{
		// The outer limits of the box we would like to search for images within
		self::$_requiredMinLatitude = $requiredMinLatitude;
		self::$_requiredMaxLatitude = $requiredMaxLatitude;
		self::$_requiredMinLongitude = $requiredMinLongitude;
		self::$_requiredMaxLongitude = $requiredMaxLongitude;
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
	public static function setPanoramioSet($panoramioSet)
	{
		self::$_panoramioSet = $panoramioSet;	
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
	 public static function setPanoramioSize($panoramioSize)
	 {
	 	self::$_panoramioImageSize = $panoramioSize;
	 }

	
	/** 
	 * Calculate the bounding box for a location via its latitude, longitude
	 */
	 protected static function _calculateBoundingBox($lat,$long)
	 {
		$minLocation = self::_calculateNewPosition($lat,$long, 225);
		self::$_requiredMinLatitude = $minLocation['latitude'];
		self::$_requiredMinLongitude = $minLocation['longitude'];
		 
		$maxLocation = self::_calculateNewPosition($lat,$long, 45);
		self::$_requiredMaxLatitude = $maxLocation['latitude'];
		self::$_requiredMaxLongitude = $maxLocation['longitude'];
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
	 protected static function _calculateNewPosition($placeLatitude, $placeLongitude, $directionBearing)
	 {
	 	$earthRadius = 6371; // Radius of the earth in kilometers
		$newLocation = array();
		$newLocation['latitude'] = rad2deg(asin(sin(deg2rad($placeLatitude)) * cos(self::$_locationDistance / $earthRadius) + cos(deg2rad($placeLatitude)) * sin(self::$_locationDistance / $earthRadius) * cos(deg2rad($directionBearing))));
		$newLocation['longitude'] = rad2deg(deg2rad($placeLongitude) + atan2(sin(deg2rad($directionBearing)) * sin(self::$_locationDistance / $earthRadius) * cos(deg2rad($placeLatitude)), cos(self::$_locationDistance / $earthRadius) - sin(deg2rad($placeLatitude)) * sin(deg2rad($newLocation['latitude']))));	 
		
		return 	$newLocation;
	 }

	 
	 /**
	  * Assemble the request data in preperation for passing to the API
	  * 
	  * @return string $APIendpoint
	  */
	  protected static function _APIendpoint()
	  {
		$APIendpoint = self::$_apiUrl . '?set=' . self::$_panoramioSet .
						'&from=' . self::$_panoramioStartingImage .
						'&to=' . (self::$_panoramioStartingImage + self::$_panoramioImageNumber) .
						'&minx=' . self::$_requiredMinLongitude  . '&miny=' . self::$_requiredMinLatitude. 
						'&maxx=' . self::$_requiredMaxLongitude . '&maxy=' . self::$_requiredMaxLatitude . 
						'&size=' . self::$_panoramioImageSize . '&order=' . self::$_panoramioOrdering;
		
		return $APIendpoint;
	  }

	  
	 /**
	  * Send a formatted string of data as a GET to the API and collect the response
	  * 
	  * @param string $apiData
	  * @return array $apiResponse
	  */
	 public static function _processRequest($args=false)
	 {
 		self::$_calculateBox = isset($args['calc_box']) ? $args['calc_box'] : true;
 		self::$_panoramioImageNumber = isset($args['img_num']) ? $args['img_num'] : 20;
 		self::$_panoramioStartingImage = isset($args['start_img']) ? $args['start_img'] : 0;
 		self::$_requiredLatitude = isset($args['lat']) ? $args['lat'] : 0;
 		self::$_requiredLongitude = isset($args['long']) ? $args['long'] : 0;

		if(self::$_calculateBox == true)
		{
			self::_calculateBoundingBox($lat,$long);
		}

	 	if(method_exists('cURLs','access_curl'))
	 	{
			$APIendpoint = self::_APIendpoint();

			$cURLs = new cURLs(array('url'=>$APIendpoint,'type'=>'data'));
			$get = $cURLs->access_curl();

			$decode = json_decode($get,true);

			if(isset($decode['photos']) AND count($decode['photos']) > 0)
			{
				return $get;
			}
			else
			{
				return null;
			}
		}
		else
		{
			return null;
		}	
	 }
 }


/**
 * new Throw PanoramioException
 */
class PanoramioException extends Exception{}