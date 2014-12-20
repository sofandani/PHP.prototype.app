<?php if ( !defined('BASEPATH')) header('Location:404');
/**
 * Simple Read files method using read()
 * --------------------------------------
 *
 * @param $dir -> Directory Name
 * @param $typefile -> Type of file is available to multi like 'js|css|txt' but must related
 * @param $matchname -> Filter file name is available to multi like 'slider.min|bounch-twice' but must related file type
 * @return Array[0]
 * @since 3.9.1
 *
 */
class StupidReadFile
{
	protected $_dirname;
	protected $_typefile;
	protected $_matchname;
	protected $_arraydata;

	function __construct($args=false)
	{
		$args = is_array($args) ? $args : null;
		$this->_dirname = isset($argum['dirname']) ? $argum['dirname'] : null;
		$this->_typefile = isset($argum['typefile']) ? $argum['typefile'] : null;
		$this->_matchname = isset($argum['matchname']) ? $argum['matchname'] : null;
	}

	public static function ofanebob_read_files($dir,$typefile,$matchname='')
	{
		if($this->_dirname == null)
		{
			throw new StupidReadingFileException('Directory name not set');
		}
		else
		{
			if($this->_typefile == null)
			{
				throw new StupidReadingFileException('File type not set');
			}
			else
			{
				$array = array();
				$d = dir($this->_dirname);
				
				while($name = $d->read()){
					
					$matchname = $this->_matchname;
					$typefile = $this->_typefile;

					$filter = empty($matchname) ? '/\.('.$typefile.')$/' : '/('.$matchname.')\.('.$typefile.')$/';
					if(!preg_match($filter, $name)) continue;
					$size = filesize($dir.$name);
					$lastmod = filemtime($dir.$name)*1000;
					$array[] = array('name'=>$name, 'size'=>$size, 'lastmod'=>$lastmod, 'url'=>$dir.$name, 'path'=>$dir);
				}

				$d->close();
				return $array;
			}
		}
	}
}

/**
 * StupidReadingFileException extends
 */
class StupidReadingFileException extends Exception{}
?>