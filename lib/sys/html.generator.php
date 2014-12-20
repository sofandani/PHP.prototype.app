<?php if ( !defined('BASEPATH')) header('Location:404');
/**
 * GenTag Class
 * HTML tag generatpr for meta,link stylesheet & script
 * @author Ofan Ebob
 * @since 2014 (v.1)
 */
class GenTag
{
	private static function _print($s)
	{
		echo $s;
	}

	public static function css($a)
	{
		if(is_array($a))
		{
			if (count($a) == count($a, COUNT_RECURSIVE)) 
			{
				return self::_buildCSS($a);
			}
			else
			{
				$css = '';
				foreach($a as $name => $value)
				{
					$css .= self::_buildCSS($value);
				}
				return $css;
			}
		}
	}

	private static function _buildCSS($a)
	{
		$print = isset($a['print']) ? $a['print'] : false;
		$rel = isset($a['rel']) ? $a['rel'] : 'stylesheet';
		$media = isset($a['media']) ? $a['media'] : 'all';
		$path = isset($a['path']) ? $a['path'] : DOMAINFIX.'/public/assets/css/';
		$href = isset($a['href']) ? $path.$a['href'] : null;

		if($href !== null)
		{
			$return = '<link media="'.$media.'" rel="'.$rel.'" href="'.$href.'" />';
			return $print == true ? self::_print($return) : $return;
		}
	}

	public static function script($a)
	{
		if(is_array($a))
		{
			if (count($a) == count($a, COUNT_RECURSIVE)) 
			{
				return self::_buildScript($a);
			}
			else
			{
				$script = '';
				foreach($a as $name => $value)
				{
					$script .= self::_buildScript($value);
				}
				return $script;
			}
		}
	}

	private static function _buildScript($a)
	{
		$print = isset($a['print']) ? $a['print'] : false;
		$type = isset($a['type']) ? $a['type'] : 'text/javascript';
		$path = isset($a['path']) ? $a['path'] : DOMAINFIX.'/public/assets/js/';
		$src = isset($a['src']) ? $path.$a['src'] : null;

		if($src !== null)
		{
			$return = '<script type="'.$type.'" src="'.$src.'"></script>';
			return $print == true ? self::_print($return) : $return;
		}
	}
}
?>