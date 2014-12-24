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
	protected $_args;

	/** @var array */
	protected $_key_bank;

	/** @var string */
	protected $_api_key;

	/** @var string */
	protected $_cache_dir;

	protected $_basepath;

	const ERROR_MSG = 'Something Wrong';
	const BASE_LANG = 'ID';
	const BASE_CITY = 'Kuningan';
	const BASE_API = 'http://api.wunderground.com/api';

	/**
	 * __construct()
	 * Definisi variable global dari data parameter
	 * @param $args
	 */
	public function __construct()
	{
		$this->_basepath = (defined('BASEPATH') ? BASEPATH : dirname(__FILE__).'/../../');

		$this->_cache_dir = $this->_basepath.'/app/weather/json';

		/** @var Kumpulan API Key */
		$this->_key_bank = array('d4c777b679398c1f',
								 'af78709edfd4ec2a',
								 'd99ad94c123332dc',
								 '884f5119fba8dcca',
								 'ea3e5444b26c226d',
								 'fc2b1beb23d8c176'
								 );

		/** @var API Key di acak */
		$this->_api_key = $this->_key_bank[array_rand($this->_key_bank)];
	}

	/**
	 * retrive_api()
	 * Fungsi akses publik setelah semua proses permintaan data
 	 * @throws WuForecastException on retrive_api()
 	 * @return stdObject convertion from json_decode()
	 */
	public function retrive_api($args=false)
	{
		$this->_args = is_array($args) ? $args : null;

		try
		{
			$cache_dir = isset($args['cache_dir']) ? $args['cache_dir'] : $this->_cache_dir;
			
			$type_save = isset($args['type_save']) ? $args['type_save'] : 'database';

			$serialize = isset($args['serialize']) ? $args['serialize'] : true;

			$cache_table = isset($args['cache_table']) ? $args['cache_table'] : 'app_cache';

			$city = isset($args['city']) ? $args['city'] : 'Kuningan';

			$expire_cache = isset($args['expire_cache']) ? $args['expire_cache'] : strtotime('+1 Hour');

			$cache = CacheHandler::save(array('method'=>array('WuForecast','_ServiceWeather'),
											  'data'=>array('key'=>$this->_api_key,'lang'=>'ID','city'=>$city),
											  'cache_expire'=>$expire_cache,
											  'cache_prefix'=>'forecast',
											  'cache_id'=>$city,
											  'cache_dir'=>$cache_dir,
											  'type_save'=>$type_save,
											  'cache_table'=>$cache_table,
											  'serialize'=>$serialize
											  )
										);


			$data = is_object($cache) ? $cache : json_decode($cache);

			$error_var = @$data->response->error;
			$results_var = @$data->response->results;

			if($error_var)
			{
				throw new WuForecastException($error_var->description);
			}
			elseif($results_var)
			{
				throw new WuForecastException(self::ERROR_MSG);
			}
			else
			{
				return $data;
			}
		}
		catch(CacheHandlerException $e)
		{
			throw new WuForecastException($e->getMessage());
		}
	}


	/**
	 * _ServiceWeather()
	 * Fungsi proteksi untuk proses permintaan data menggunakan WUnderground API endpoint
 	 * @return cURLs() retrive data
	 */
	public function _ServiceWeather($parm=false)
	{
		$data = $parm == false ? $this->_args : $parm;

		if($data == null)
		{
			return false;
		}
		else
		{
		 	if(method_exists('cURLs','access_curl'))
		 	{
				$server = self::BASE_API;
				$key = isset($data['key']) ? $data['key'] : $this->_api_key;
				$lang = isset($data['lang']) ? $data['lang'] : self::BASE_LANG;
				$city = isset($data['city']) ? $data['city'] : self::BASE_CITY;
				$city = preg_replace('/ /','',$city);
				$forecast = isset($data['forecast']) ? ($data['forecast'] == true ? 'forecast/' : '') : '';
				$API_ENDPOINT = $server.'/'.$key.'/conditions/'.$forecast.'lang:'.$lang.'/q/'.$city.'.json';

				$cURL = new cURLs(array('url'=>$API_ENDPOINT,'type'=>'data'));
				return $cURL->access_curl();
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