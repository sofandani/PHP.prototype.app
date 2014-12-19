<?php
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
function ofanebob_read_files($dir,$typefile,$matchname='') {
	if(!$typefile) exit('Jenis file harus di definisikan!');
	$arr = array();
	$d = dir($dir);
	while($name = $d->read()){
		$filter = empty($matchname) ? '/\.('.$typefile.')$/' : '/('.$matchname.')\.('.$typefile.')$/';
		if(!preg_match($filter, $name)) continue;
		$size = filesize($dir.$name);
		$lastmod = filemtime($dir.$name)*1000;
		$arr[] = array('name'=>$name, 'size'=>$size, 'lastmod'=>$lastmod, 'url'=>$dir.$name, 'path'=>$dir);
	}
	$d->close();
	return $arr;
}
?>