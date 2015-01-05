<?php if ( !defined('BASEPATH')) header('Location:/404');
/**
 * WuForecast Class
 * Weather & Forecast using API Wunderground Service
 * @author Ofan Ebob
 * @since 2014 (v.2) - Pengembangan dari Weather Widget www.kuninganasri.com
 * @copyright GNU & GPL license
 */
class WuForecast
{
	/** @var array */
	protected static $_args;

	/** @var array */
	protected static $_key_bank;

	/** @var string */
	protected static $_api_key;

	/** @var string */
	protected static $_cache_dir;

	protected static $_basepath;

	const ERROR_MSG = 'Something Wrong';
	const BASE_LANG = 'ID';
	const BASE_CITY = 'Kuningan';
	const BASE_API = 'http://api.wunderground.com/api';
	

	/**
	 * retrive_api()
	 * Fungsi akses publik setelah semua proses permintaan data
 	 * @throws WuForecastException on retrive_api()
 	 * @return stdObject convertion from json_decode()
	 */
	public static function retrive_api($args=false)
	{
		self::$_args = is_array($args) ? $args : null;

		/** @var path direktori default */
		self::$_basepath = (defined('BASEPATH') ? BASEPATH : dirname(__FILE__).'/../../');

		/** @var direktori untuk file cache */
		self::$_cache_dir = self::$_basepath.'/app/weather/json';

		self::_api_key();

		try
		{
			$cache_dir = isset($args['cache_dir']) ? $args['cache_dir'] : self::$_cache_dir;
			
			$type_save = isset($args['type_save']) ? $args['type_save'] : 'database';

			$serialize = isset($args['serialize']) ? $args['serialize'] : true;

			$table_cache = isset($args['table_cache']) ? $args['table_cache'] : 'app_cache';

			$city = isset($args['city']) ? $args['city'] : 'Kuningan';

			$expire_cache = isset($args['expire_cache']) ? $args['expire_cache'] : strtotime('+1 Hour');
			
			$api_key = isset($args['key']) ? $args['key'] : self::_api_key();

			$param = array(	'method'=>array('WuForecast','_ServiceWeather'),
							'data'=>array('key'=>$api_key,'lang'=>'ID','city'=>$city),
							'cache_expire'=>$expire_cache,
							'cache_prefix'=>'forecast',
							'cache_id'=>$city,
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
			throw new WuForecastException($e->getMessage());
		}
	}


	private static function _api_key()
	{
		/** @var Kumpulan API Key */
		self::$_key_bank = array('d4c777b679398c1f',
								 'af78709edfd4ec2a',
								 'd99ad94c123332dc',
								 '884f5119fba8dcca',
								 'ea3e5444b26c226d',
								 'fc2b1beb23d8c176'
								 );

		/** @var API Key di acak */
		self::$_api_key = self::$_key_bank[array_rand(self::$_key_bank)];
	}


	/**
	 * _ServiceWeather()
	 * Fungsi proteksi untuk proses permintaan data menggunakan WUnderground API endpoint
 	 * @return cURLs() retrive data
	 */
	public static function _ServiceWeather($parm=false)
	{
		$data = $parm == false ? self::$_args : $parm;

		if($data == null)
		{
			return false;
		}
		else
		{
		 	if(method_exists('cURLs','access_curl'))
		 	{
				$server = self::BASE_API;
				$key = isset($data['key']) ? $data['key'] : self::$_api_key;
				$lang = isset($data['lang']) ? $data['lang'] : self::BASE_LANG;
				$city = isset($data['city']) ? $data['city'] : self::BASE_CITY;
				$city = preg_replace('/ /','',$city);
				$forecast = isset($data['forecast']) ? ($data['forecast'] == true ? 'forecast/' : '') : '';
				$API_ENDPOINT = $server.'/'.$key.'/conditions/'.$forecast.'lang:'.$lang.'/q/'.$city.'.json';

				$cURL = new cURLs(array('url'=>$API_ENDPOINT,'type'=>'data'));
				$get = $cURL->access_curl();

				$decode = json_decode($get,true);

				if(isset($decode['response']['error']) || 
					isset($decode['response']['results']))
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
				return false;
			}
		}
	}
}


/** Define Exception Extends Class */
class WuForecastException extends Exception{}
?>