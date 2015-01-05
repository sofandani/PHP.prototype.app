<?php if ( !defined('BASEPATH')) header('Location:/404');
/**
 * GoogleGeocode Class
 * Geocode converter from city Name via google maps geocode
 * @author Ofan Ebob
 * @since 2014 (v.1)
 * @copyright GNU & GPL license
 */
class GoogleGeocode
{
	protected static $_args;
	protected static $_cache_expire;
	protected static $_cache_dir;

	const BASE_CITY = 'Kuningan, Jawa Barat';
	const CACHE_DIR = 'cache';

	public static function get_geocode($args=false)
	{
		self::$_args = is_array($args) ? $args : null;

		self::$_cache_expire = strtotime('+6 Month');

		try
		{
			$city = isset($args['city']) ? $args['city'] : self::BASE_CITY;
			$city = preg_replace('/ /','',$city);

			$cache_dir = isset($args['cache_dir']) ? $args['cache_dir'] : (defined('BASEPATH') ? BASEPATH.'/' : dirname(__FILE__).'/../').self::CACHE_DIR;
			
			$expire_cache = isset($args['cache_expire']) ? $args['cache_expire'] : self::$_cache_expire;

			$typedata = isset($args['typedata']) ? $args['typedata'] : 'json';

			$type_save = isset($args['type_save']) ? $args['type_save'] : 'database';

			$serialize = isset($args['serialize']) ? $args['serialize'] : true;

			$table_cache = isset($args['table_cache']) ? $args['table_cache'] : 'cache';

			$serialize = isset($args['serialize']) ? $args['serialize'] : true;

			// Cache Handler
			$cache = CacheHandler::save(array('method'=>array('GoogleGeocode','_RetrieveGeocode'),
											  'data'=>array('typedata'=>$typedata,'city'=>$city),
											  'cache_expire'=>$expire_cache,
											  'cache_prefix'=>'geolocation',
											  'cache_id'=>$city,
											  'cache_dir'=>$cache_dir,
											  'type_save'=>$type_save,
											  'table_cache'=>$table_cache,
											  'serialize'=>$serialize
											  )
										);

			$data = is_object($cache) ? $cache : json_decode($cache);

			return $data;
		}
		catch(CacheHandlerException $e)
		{
			throw new GoogleGeocodeException($e->getMessage().' from GeoCode');
		}
	}


	public static function _RetrieveGeocode($parm=false)
	{
		$data = $parm == false ? self::$_args : $parm;

		if($data == null)
		{
			return $data;
		}
		else
		{
			if(method_exists('cURLs','access_curl'))
			{
				$server = 'https://maps.googleapis.com/maps/api/geocode';
				$city = isset($data['city']) ? $data['city'] : self::BASE_CITY;
				$city = preg_replace('/\,/','',$city);
				$typedata = isset($data['typedata']) ? $data['typedata'] : 'json';
				$API_ENDPOINT = $server.'/'.$typedata.'?address='.urlencode($city);
				$cURLs = new cURLs(array('url'=>$API_ENDPOINT,'type'=>'data'));
				$get = $cURLs->access_curl();

				$decode = json_decode($get,true);

				if( count($decode['results']) > 1 || 
					isset($decode['status']) && 
					($decode['status'] != "OK" || 
					$decode['status'] == "ZERO_RESULTS") 
					)
				{
					return null;
				}
				else
				{
					return $get;
				}
			}
			else
			{
				return null;
			}
		}
	}
}


/**
 * GoogleGeocodeException extends
 */
class GoogleGeocodeException extends Exception{}
?>