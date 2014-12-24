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
	protected $_args;
	protected $_cache_expire;
	protected $_cache_dir;

	const BASE_CITY = 'Kuningan, Jawa Barat';
	const CACHE_DIR = 'cache';

	public function __construct()
	{
		$this->_cache_expire = strtotime('+6 Month');
	}

	public function get_geocode($args=false)
	{
		$this->_args = is_array($args) ? $args : null;

		try
		{
			$city = isset($args['city']) ? $args['city'] : self::BASE_CITY;
			$city = preg_replace('/ /','',$city);

			$cache_dir = isset($args['cache_dir']) ? $args['cache_dir'] : (defined('BASEPATH') ? BASEPATH.'/' : dirname(__FILE__).'/../').self::CACHE_DIR;
			
			$expire_cache = isset($args['cache_expire']) ? $args['cache_expire'] : $this->_cache_expire;

			$typedata = isset($args['typedata']) ? $args['typedata'] : 'json';

			// Cache Handler
			$data = CacheHandler::save(array('method'=>array('GoogleGeocode','_RetrieveGeocode'),
											 'data'=>array('typedata'=>$typedata,'city'=>$city),
											 'cache_expire'=>$expire_cache,
											 'cache_prefix'=>'geolocation',
											 'cache_id'=>$city,
											 'cache_dir'=>$cache_dir
											 )
										);

			$gc = json_decode($data,true);

			if( isset($gc['status']) AND ($gc['status'] != "OK" OR $gc['status'] == "ERO_RESULTS") )
			{
				throw new GoogleGeocodeException('Failed Retreive Data.');
			}
			else
			{
				return json_decode($data);
			}
		}
		catch(CacheHandlerException $e)
		{
			throw new GoogleGeocodeException($e->getMessage());
		}
	}


	public function _RetrieveGeocode($parm=false)
	{
		$data = $parm == false ? $this->_args : $parm;

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
				return $cURLs->access_curl();
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