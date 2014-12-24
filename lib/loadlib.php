<?php if ( !defined('BASEPATH')) header('Location:404');
/**
 * Libs Class
 * Library function, class & method loader
 * @author Ofan Ebob
 * @since 2014 (v.1)
 */
class Libs
{
	public static function loader_3rd($data)
	{
		return self::load($data,'3rd');
	}

	public static function load($data,$vendor='sys')
	{
		if(is_array($data))
		{
			foreach($data as $lib){
				$lib_data = LIBPATH.'/'.$vendor.'/'.$lib.'.php';
				if(file_exists($lib_data)) require_once($lib_data);
			}
		}
	}
}
?>