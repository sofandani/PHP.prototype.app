<?php
class GoogleGeocode
{
	protected $_args;
	protected $_cache_expire;

	const BASE_CITY = 'Kuningan, Jawa Barat';
	const CACHE_DIR = 'cache';

	public function __construct($args=false)
	{
		$this->_args = is_array($args) ? $args : null;
		$this->_cache_expire = strtotime('+1 Month');
	}

	public function get_geocode()
	{
		$gc = $this->_CacheGeocode();
		if($gc == null)
		{
			throw new GoogleGeocodeException('Failed Retrive Data.');
		}
		else
		{
			return $gc;
		}
	}

	protected function _CacheGeocode()
	{
		$argums = $this->_args;
		if($argums == null)
		{
			return $argum;
		}
		else
		{
			$city = isset($argums['city']) ? $argums['city'] : self::BASE_CITY;
			$basepath = defined('BASEPATH') ? BASEPATH : dirname(__FILE__).'/../';
			$cache_dir = $basepath.'/'.self::CACHE_DIR;
			$cache_expire = isset($argums['cache_expire']) ? $argums['cache_expire'] : $this->_cache_expire;
			$stored = $cache_dir.'/geolocation-'.md5($city).'.json';

			if( file_exists($stored) AND ( filemtime($stored) < strtotime('now') ) )
			{
				unlink($stored);
			}

			if( !file_exists($stored) )
			{
				$rc = $this->_RetriveGeocode();

				if($rc == false)
				{
					$data = null;
				}
				else
				{
					$gc = json_decode($rc,true);
					if( isset($gc['status']) AND ($gc['status'] != "OK" OR $gc['status'] == "ERO_RESULTS") )
					{
						return null;
					}
					else
					{
						@file_put_contents($stored, $rc);
						touch($stored, $cache_expire);

						if(function_exists('ArrayToObject') == false)
						{
							require_once(dirname(__FILE__).'/array.convert.php');
						}

						return json_decode($rc);
					}
				}
			}
			else
			{
				$fgc = @file_get_contents($stored);
				return json_decode($fgc);
			}
		}
	}

	protected function _RetriveGeocode()
	{
		$argums = $this->_args;
		if($argums == null)
		{
			return $argum;
		}
		else
		{
			if(method_exists('cURLs','access_curl'))
			{
				$server = 'https://maps.googleapis.com/maps/api/geocode';
				$city = isset($argums['city']) ? $argums['city'] : self::BASE_CITY;
				$typedata = isset($argums['typedata']) ? $argums['typedata'] : 'json';
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