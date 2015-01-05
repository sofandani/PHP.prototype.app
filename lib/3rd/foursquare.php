<?php if ( !defined('BASEPATH')) header('Location:/404');
/**
 * Foursquare Class
 * Foursquare API Request
 * @author Ofan Ebob
 * @since 2014 (v.2)
 * @copyright GNU & GPL license
 */
class Foursquare
{
	/** @var array */
	protected static $_args;

	/** @var array */
	protected static $_key_bank;

	/** @var string */
	protected static $_api_key;

	/** @var string */
	protected static $_cache_dir;

	/** @var basepath */
	protected static $_basepath;

	const ERROR_MSG = 'Something Wrong';
	const BASE_LOCALE = 'id';
	const BASE_API = 'https://api.foursquare.com';
	const API_VERSION = 2;
	const API_ENDPOINT = 'venues/search?near=';
	

	/**
	 * retrive_api()
	 * Fungsi akses publik setelah semua proses permintaan data
 	 * @throws FoursquareException on retrive_api()
 	 * @return stdObject convertion from json_decode()
	 */
	public static function retrive_api($args=false)
	{
		/** @var $args params */
		self::$_args = is_array($args) ? $args : null;

		/** @var path direktori default */
		self::$_basepath = (defined('BASEPATH') ? BASEPATH : dirname(__FILE__).'/../../');

		/** @var direktori untuk file cache */
		self::$_cache_dir = self::$_basepath.'/app/venue/json';

		try
		{
			$cache_dir = isset($args['cache_dir']) ? $args['cache_dir'] : self::$_cache_dir;
			
			$type_save = isset($args['type_save']) ? $args['type_save'] : 'database';

			$serialize = isset($args['serialize']) ? $args['serialize'] : true;

			$table_cache = isset($args['table_cache']) ? $args['table_cache'] : 'app_cache';

			$endpoint_type = isset($args['endpoint_type']) ? self::endpoint_type($args['endpoint_type']) : self::endpoint_type('search') ;

			$expire_cache = isset($args['expire_cache']) ? $args['expire_cache'] : strtotime('+1 Hour');

			$api_key = isset($args['key']) ? $args['key'] : self::_api_key();

			$query = isset($args['query']) ? $args['query'] : 'Kuningan, Jawa Barat';

			$final_query = self::_geocode($args['endpoint_type'],$query,$type_save);

			$param = array(	'method'=>array('Foursquare','_ServiceFoursquare'),
							'data'=>array('key'=>$api_key,
										  'locale'=>self::BASE_LOCALE,
										  'query'=>$final_query,
										  'endpoint_type'=>$endpoint_type
							),
							'cache_expire'=>$expire_cache,
							'cache_prefix'=>'foursquare-'.$args['endpoint_type'],
							'cache_id'=>$query,
							'cache_dir'=>$cache_dir,
							'type_save'=>$type_save,
							'table_cache'=>$table_cache,
							'serialize'=>$serialize
							);

			$cache = CacheHandler::save($param);

			$data = is_object($cache) ? $cache : json_decode($cache);

			return $data;
		}
		catch(CacheHandlerException $e)
		{
			throw new FoursquareException($e->getMessage().' From Foursquare');
		}
	}


	private static function _api_key()
	{
		/** @var Kumpulan API Key */
		self::$_key_bank = array(array('P20APVP31JG3U0UJC4ZPWSSWW5GMP4WJ014TA5JAGWYXJBLD', 'OQIS4CBVG1TNQCRQMWOBHLOCZMCP5ZKPCF1AMXBS13EI5MEE'),
								 array('44KWZR2C3HVDWSTGPEOEHFTNA2DHL32BKDCWUJC3HD1ZDHZF', 'LXSUSXZWIMARWLTVBZT3HVUYOA5RN0SNR1NUJF4AFRQRBWQ4'),
								 array('A2BZQ3VFIILKB0KA1BMLV1DXS5M0E3BSNRW1FVDOXI20OELK', 'OWKK1WFBYICBUBL0VC5UF4UNTHAC0TPE0LY2LJW1C1EYN31I'),
								 array('AXXY1AEIL1MVUIS2JKJTSJEMBLKX0IFE223EDVQPZFBR42QB', 'U5TQYKX3F1CNOVH1PT5QCKSM4WSL2H0NEKXUGRCHTOJTYHIB'),
								 array('A4NVEI2FKX3QR5CBC24S4TIKTY1WXWJ2ZSO5VPGLMKARPM0I', 'QODM2JM2Z4BVZ5DXVI0F2U050DSNEN2B2B5LHTDTOLUD5CFX')
							);

		/** @var API Key di acak */
		self::$_api_key = self::$_key_bank[array_rand(self::$_key_bank)];
	}


	protected static function _geocode($endpoint='',$query='',$type_save='')
	{
		if( empty($endpoint) AND empty($query) AND empty($type_save) )
		{
			return null;
		}
		else
		{
			if(in_array($endpoint,array('trending','explore')))
			{
				try
				{
					$geo_param = array('city'=>$query,'typedata'=>'json','type_save'=>$type_save);
					$get_geocode = GoogleGeocode::get_geocode($geo_param);

					$lat = floatval($get_geocode->results[0]->geometry->location->lat);
					$lng = floatval($get_geocode->results[0]->geometry->location->lng);

					return $lat.','.$lng;
				}
				catch(GoogleGeocodeException $e)
				{
					throw new FoursquareException($e->getMessage().' Geo/Foursquare');
				}
			}
			else
			{
				return urlencode($query);
			}
		}
	}

	/**
	 * endpoint_type()
	 * Menetapkan jenis endpoint yg tersedia di API Foursquare
	 * @param $type
 	 * @return strings
	 */
	public static function endpoint_type($type='search')
	{
		switch ($type) {
			case 'search':
				return 'venues/search?near=';
				break;
			
			case 'explore':
				return 'venues/explore?venuePhotos=1&ll=';
				break;

			case 'trending':
				return 'venues/categories?venuePhotos=1&ll=';
				break;

			case 'user':
				return 'user/self';
				break;

			case 'venue':
				return 'venues/';
				break;

			case 'categories':
				return 'venues/categories?';
				break;

			case 'tips':
				return 'tips/';
				break;

			default:
				return self::API_ENDPOINT;
				break;
		}
	}


	/**
	 * _ServiceFoursquare()
	 * Fungsi untuk meminta data dari API endpoint Foursquare
 	 * @return cURLs() retrive data
	 */
	public static function _ServiceFoursquare($args=false)
	{
		$data = $args == false ? self::$_args : $args;

		if($data == null)
		{
			return false;
		}
		else
		{
		 	if(method_exists('cURLs','access_curl'))
		 	{
				$server = self::BASE_API;
				$api_version = self::API_VERSION;
				$key = isset($data['key']) ? $data['key'] : self::$_api_key;
				$locale = isset($data['locale']) ? $data['locale'] : self::BASE_LOCALE;
				$endpoint_type = isset($data['endpoint_type']) ? $data['endpoint_type'] : self::API_ENDPOINT;
				$query = isset($data['query']) ? $data['query'] : null;

				$API_ENDPOINT = $server.'/v'.$api_version.'/'.$endpoint_type.$query.'&client_id='.$key[0].'&client_secret='.$key[1].'&locale='.$locale.'&v='.date('Ymd');

				$cURL = new cURLs(array('url'=>$API_ENDPOINT,'type'=>'data'));
				$get = $cURL->access_curl();

				$decode = json_decode($get,true);

				if( $decode['meta']['code'] == 200 || 
					count($decode['response']['venues']) > 0 || 
					count($decode['response']['groups']) > 0)
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
}


/** Define Exception Extends Class */
class FoursquareException extends Exception{}
?>