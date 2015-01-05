<?php if ( !defined('BASEPATH')) header('Location:/404');
/**
 * CacheHandler Class
 * Menangani proses penyimpanan cache berupa file atau database
 * @author Ofan Ebob
 * @since 2014 (v.1)
 * @copyright GNU & GPL license
 */
class CacheHandler
{
	protected static $_args;
	protected static $_cache_expire;
	protected static $_cache_prefix;
	protected static $_cache_name;
	protected static $_cache_id;

	const CACHE_DIR = 'cache';

	function __construct()
	{
		$this->_cache_expire = strtotime('+1 Day');
		$this->_cache_prefix = 'prototype';
		$this->_cache_id = md5(date('Y-m-d h:i:s'));
	}


	/**
	 * save()
	 * Fungsi akses publik setelah semua proses permintaan data
 	 * @throws CacheHandlerException
 	 * @return $save
	 */
	public static function save($args=false)
	{
		self::$_args = is_array($args) ? $args : null;
		$argum = self::$_args;

		if($argum == null)
		{
			throw new CacheHandlerException('Unknown Parameter Cache.');
		}
		else
		{
			$cache_prefix = isset($argum['cache_prefix']) ? $argum['cache_prefix'] : self::$_cache_prefix;

			$cache_id = isset($argum['cache_id']) ? $argum['cache_id'] : self::$_cache_id;
			$cache_id = preg_replace('/ /','',$cache_id);

			$type_save = isset($argum['type_save']) ? $argum['type_save'] : 'file';

			self::$_cache_expire = isset($argum['cache_expire']) ? $argum['cache_expire'] : self::$_cache_expire;

			self::$_cache_name = $cache_prefix.'-'.md5(strtolower($cache_id));

			switch($type_save)
			{
				case 'file':
				$save = self::_CacheToFile();
				break;
				case 'database':
				$save = self::_CacheToDatabase();
				break;
				default:
				$save = null;
			}

			if($save == null)
			{
				throw new CacheHandlerException('Error Save Cache.');
			}
			else
			{
				return $save;
			}
		}
	}


	/**
	 * _CacheToFile()
	 * Fungsi menangani penyimpanan cache berupa file
 	 * @return $cache OR NULL
	 */
	protected static function _CacheToFile()
	{
		$argum = self::$_args;

		if($argum == null)
		{
			return $argum;
		}
		else
		{
			$cache_name = self::$_cache_name;
			$cache_expire = self::$_cache_expire;

			$basepath = defined('BASEPATH') ? BASEPATH : dirname(__FILE__).'/../../';
			$cache_dir = isset($argum['cache_dir']) ? $argum['cache_dir'] : $basepath.'/'.self::CACHE_DIR;
			$formatted = isset($argum['format']) ? $argum['format'] : 'json';

			$stored = $cache_dir.'/'.$cache_name.'.'.$formatted;

			// Buat file cache baru jika file tidak ditemukan di direktori
			if( file_exists($stored) AND ( filemtime($stored) > strtotime('now') ) )
			{
				// Nilai data dari lokal file cache
				return @file_get_contents($stored);
			}
			else
			{
				$method = isset($argum['method']) ? $argum['method'] : false;
				$cache_data = isset($argum['data']) ? $argum['data'] : null;

				if( ($method==false AND $cache_data==false) ||
					 method_exists($method[0],$method[1])==false || 
					 is_callable($method)==false || 
					 (is_array($method)==false AND function_exists($method)==false)
				  )
				{
					return null;
				}
				else
				{
					$cache = call_user_func_array($method, array($cache_data));

					// Jika hasil data API false maka di return null
					if($cache == null)
					{
						return null;
					}
					else
					{
						// Buat file cache
						@file_put_contents($stored, $cache);

						// rubah meta time nya
						touch($stored, $cache_expire);

						// Definisikan nilai $cache
						return $cache;
					}
				}
			}
		}
	}


	/**
	 * _CacheToDatabase()
	 * Fungsi menangani penyimpanan cache berupa MySQL atau Database
 	 * @return $cache OR NULL
	 */
	protected static function _CacheToDatabase()
	{
		$argum = self::$_args;

		if($argum == null)
		{
			return $argum;
		}
		else
		{
			$serialize = isset($argum['serialize']) ? $argum['serialize'] : true;

			$method = isset($argum['method']) ? $argum['method'] : false;

			$method_data = isset($argum['data']) ? $argum['data'] : null;

			$table_cache = isset($argum['table_cache']) ? $argum['table_cache'] : 'app_cache';

			$cache_name = self::$_cache_name;

			$cache_expire = self::$_cache_expire;

			$prm_read = "WHERE cache_name='{$cache_name}'";
			$data_read = array('tbl'=>$table_cache,'row'=>'*','prm'=>$prm_read);
			$sql_read = AccessCRUD($data_read,'read');

			// Cek ketersediaan cache di database
			if( $sql_read!=false )
			{
				if( $sql_read->num_rows > 0 )
				{
					$sql_array = $sql_read->fetch_array();
					
					if( $sql_array['cache_expire'] < strtotime('now') )
					{
						if( ($method==false AND $method_data==false) ||
							 method_exists($method[0],$method[1])==false || 
							 is_callable($method)==false || 
							 (is_array($method)==false AND function_exists($method)==false)
						  )
						{
							return null;
						}
						else
						{
							$cache_update = call_user_func_array($method, array($method_data));

							if($cache_update == null)
							{
								return null;
							}
							else
							{
								if(is_array($cache_update))
								{
									$cache_result = $cache_update;
								}
								elseif(is_object($cache_update))
								{
									$cache_result = ObjectToArray($cache_update);
								}
								else
								{
									$cache_result = json_decode($cache_update);
								}

								$cache_data = ($serialize == true ? serialize($cache_result) : $cache_result);

								$prm_update = array('cache_data'=>$cache_data,'cache_expire'=>$cache_expire);

								$data_update = array('tbl'=>$table_cache,'prm'=>$prm_update,'con'=>$prm_read);
								
								AccessCRUD($data_update,'update');

								return $cache_update;
							}
						}
					}
					else
					{
						return ($serialize == true ? unserialize($sql_array['cache_data']) : $sql_array['cache_data']);
					}
				}
				else
				{
					if( ($method==false AND $method_data==false) ||
						 method_exists($method[0],$method[1])==false || 
						 is_callable($method)==false || 
						 (is_array($method)==false AND function_exists($method)==false)
					  )
					{
						return null;
					}
					else
					{
						$cache_create = call_user_func_array($method, array($method_data));

						// Jika hasil data API false maka di return null
						if($cache_create == null)
						{
							return null;
						}
						else
						{
							if(is_array($cache_create))
							{
								$cache_result = $cache_create;
							}
							elseif(is_object($cache_create))
							{
								$cache_result = ObjectToArray($cache_create);
							}
							else
							{
								$cache_result = json_decode($cache_create);
							}

							$cache_data = ($serialize == true ? serialize($cache_result) : $cache_result);

						//var_dump($cache);

							$prm_save = array('cache_name'=>$cache_name,'cache_data'=>$cache_data,'cache_expire'=>$cache_expire);
							
							$data_save = array('tbl'=>$table_cache,'prm'=>$prm_save);
							
							AccessCRUD($data_save,'create');

							return $cache_create;
						}
					}
				}
			}
		}
	}


	/**
	 * drop()
	 * Fungsi akses publik untuk menghapus data cache
 	 * @throws CacheHandlerException
 	 * @return $cache
	 */
	public static function drop($args=false)
	{
		$argum = self::$_args;

		if($argum == null)
		{
			throw new CacheHandlerException('Unknown Parameter Cache.');
		}
		else
		{
			$cache_name = self::$_cache_name;
			switch($type_save)
			{
				case 'file':
				$save = self::_DropCacheFile();
				break;
				case 'database':
				$save = self::_DropCacheDatabase();
				break;
				default:
				$save = null;
			}

			if($save == null)
			{
				throw new CacheHandlerException('Failed Drop Cache.');
			}
			else
			{
				return true;
			}
		}
	}


	/**
	 * _DropCacheFile()
	 * Fungsi menangani penghapusan cache berupa file
 	 * @return true OR null
	 */
	protected static function _DropCacheFile()
	{
		$cache_name = self::$_cache_name;

		$basepath = defined('BASEPATH') ? BASEPATH : self::BASE_PATH;
		$cache_dir = $basepath.'/'.self::CACHE_DIR;
		$stored = $cache_dir.'/'.$cache_name.'.json';

		if( file_exists($stored) AND ( filemtime($stored) < strtotime('now') ) )
		{
			unlink($stored);
			return true;
		}
		else{
			return null;
		}
	}


	/**
	 * _DropCacheFile()
	 * Fungsi menangani penghapusan cache berupa MySQL atau Database
 	 * @return true OR null
	 */
	protected static function _DropCacheDatabase()
	{
		$argum = self::$_args;

		if($argum == null)
		{
			return $argum;
		}
		else
		{
			$table_cache = isset($argum['table_cache']) ? $argum['table_cache'] : 'app_cache';

			$cache_name = self::$_cache_name;
			$prm = "WHERE cache_name='{$cache_name}'";

			$data = array('tbl'=>$table_cache,'con'=>$prm);
			$drop = AccessCRUD($data,'update');

			if($drop == false)
			{
				return null;
			}
			else
			{
				return true;
			}
		}
	}
}

/**
 * CacheHandlerException extends
 */
class CacheHandlerException extends Exception{}
?>