<?php if ( !defined('BASEPATH')) header('Location:404');
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

	const ERROR_MSG = 'Something Wrong';
	const BASE_LANG = 'ID';
	const BASE_CITY = 'Kuningan';

	/**
	 * __construct()
	 * Definisi variable global dari data parameter
	 * @param $args
	 */
	public function __construct($args)
	{
		$this->_args = is_array($args) ? $args : null;

		$this->_cache_dir = BASEDIR.'/json';

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
	public function retrive_api()
	{
		// Definisikan nilai $data dari _CacheAPI()
		$data = $this->_CacheAPI();

		// Jika nilai $data null maka throw dipanggil
		if($data == null)
		{
			throw new WuForecastException(self::ERROR_MSG);
		}
		else
		{
			// Jika nilai $data bukan null maka dirbuah ke json_decode()
			$decode = json_decode($data, true);

			// Jika ditemukan error pada response json_decode maka di return ke teks error
			if( isset($decode['response']['error']) )
			{
				throw new WuForecastException($decode['response']['error']['description']);
			}
			elseif( isset($decode['response']['results']) )
			{
				throw new WuForecastException(self::ERROR_MSG);
			}
			else
			{
				// Jika tidak ada error pada response maka data di konversi ke stdObject
				return ArrayToObject($decode);
			}
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
		$city = preg_replace('/ /','',$city);
		$stored = $cache_dir.'/forecast-'.md5($city).'.json';
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
			$sw = $this->_ServiceWeather();

			// Jika hasil data API false maka di return null
			if($sw == false)
			{
				$data = null;
			}
			else
			{
				// Buat file cache & rubah meta time nya
				@file_put_contents($stored, $sw);
				touch($stored, $expire_cache);

				// Definisikan nilai $data
				$data = $sw;
			}
		}
		else
		{
			// Nilai data dari lokal file cache
			$data = @file_get_contents($stored);
		}

		return $data;
	}

	/**
	 * _SendToDatabase()
	 * Fungsi proteksi untuk cache data API ke database
 	 * @return Access_CRUD()
	 */
	protected function _SendToDatabase()
	{
		$data = array();
		return Access_CRUD($data,'create');
	}

	/**
	 * _ServiceWeather()
	 * Fungsi proteksi untuk proses permintaan data menggunakan WUnderground API endpoint
 	 * @return cURLs() retrive data
	 */
	protected function _ServiceWeather()
	{
		$data = $this->_args;

		if($data == null)
		{
			return false;
		}
		else
		{
		 	if(method_exists('cURLs','access_curl'))
		 	{
				$API_KEY = $this->_api_key;
				$server = 'http://api.wunderground.com/api';
				$key = isset($data['key']) ? $data['key'] : $API_KEY;
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