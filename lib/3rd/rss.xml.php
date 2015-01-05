<?php if ( !defined('BASEPATH')) header('Location:/404');
/**
 * RSS for PHP - small and easy-to-use library for consuming an RSS Feed
 * Adding default URL/Server for data request from http://news.google.com
 *
 * @author David Grudl
 * @author Change httpRequest to _RetrieveXML using cURLs class (dir="vendor/curl.php")
 * @copyright Copyright (c) 2008 - 2014 David Grudl & Ofan Ebob
 * @license New BSD License
 * @version 1.1
 */
class NewsGoogleFeed
{
	/** @var int */
	public static $cacheExpire = 86400; // 1 day

	/** @var SimpleXMLElement */
	protected $xml;


	/**
	 * Returns property value. Do not call directly.
	 * @param  string  tag name
	 * @return SimpleXMLElement
	 */
	public function __get($name)
	{
		return $this->xml->{$name};
	}


	/**
	 * Sets value of a property. Do not call directly.
	 * @param  string  property name
	 * @param  mixed   property value
	 * @return void
	 */
	public function __set($name, $value)
	{
		throw new NewsGoogleFeedException("Cannot assign to a read-only property '$name'.");
	}
	

	/**
	 * Loads RSS channel.
	 * @param  string  RSS feed URL
	 * @param  string  optional user name
	 * @param  string  optional password
	 * @return Feed
	 * @throws NewsGoogleFeedException
	 */
	public static function loadRss($args=false)
	{	
		if(isset($args['type_save']) AND $args['type_save']=='file')
		{
			$xml = new SimpleXMLElement(self::_RetrieveXML($args), LIBXML_NOWARNING | LIBXML_NOERROR);
		}
		else
		{
			$xml = self::_RetrieveXML($args);
		}

		if(!$xml->channel)
		{
			throw new NewsGoogleFeedException('Invalid channel.');
		}

		if(isset($args['type_save']) AND $args['type_save']=='file')
		{
			self::adjustNamespaces($xml->channel);
		}

		foreach ($xml->channel->item as $item)
		{
			// converts namespaces to dotted tags
			if(isset($args['type_save']) AND $args['type_save']=='file')
			{
				self::adjustNamespaces($item);
			}

			// generate 'timestamp' tag
			if(isset($item->{'dc:date'}))
			{
				$item->timestamp = strtotime($item->{'dc:date'});
			}
			elseif(isset($item->pubDate))
			{
				$item->timestamp = strtotime($item->pubDate);
			}
		}

		$feed = new self;
		$feed->xml = $xml->channel;
		return $feed;
	}


	/**
	 * Loads Atom channel.
	 * @param  string  Atom feed URL
	 * @param  string  optional user name
	 * @param  string  optional password
	 * @return Feed
	 * @throws NewsGoogleFeedException
	 */
	public static function loadAtom($args=false)
	{
		if(isset($args['type_save']) AND $args['type_save']=='file')
		{
			$xml = new SimpleXMLElement(self::_RetrieveXML($args), LIBXML_NOWARNING | LIBXML_NOERROR);
		}
		else
		{
			$xml = self::_RetrieveXML($args);
		}

		if(!in_array('http://www.w3.org/2005/Atom', $xml->getDocNamespaces(), TRUE))
		{
			throw new NewsGoogleFeedException('Invalid channel.');
		}

		// generate 'timestamp' tag
		foreach ($xml->entry as $entry)
		{
			$entry->timestamp = strtotime($entry->updated);
		}

		$feed = new self;
		$feed->xml = $xml;
		return $feed;
	}


	/**
	 * Retrive XML.
	 * @param  string query
	 * @return string
	 * @throws NewsGoogleFeedException
	 */
	private static function _RetrieveXML($args=false)
	{
		if(is_array($args))
		{
			try
			{
				$basePath = (defined('BASEPATH') ? BASEPATH : dirname(__FILE__).'/../../');
				
				$cache_dir = $basePath.'/app/news/xml';

				$expire_cache = isset($args['expire_cache']) ? $args['expire_cache'] : strtotime('+1 Day');
				$query = isset($args['query']) ? $args['query'] : 'Kuningan, Jawa Barat';
				$table_cache = isset($args['table_cache']) ? $args['table_cache'] : 'app_cache';
				$serialize = isset($args['serialize']) ? $args['serialize'] : true;
				$type_save = isset($args['type_save']) ? $args['type_save'] : 'database';

				// Cache Handler
				$param = array('method'=>array( 'NewsGoogleFeed','_RequestData'),
												'data'=>array('query'=>$query,'type_save'=>$type_save),
												'cache_expire'=>$expire_cache,
												'cache_prefix'=>'feed',
												'cache_id'=>$query,
												'cache_dir'=>$cache_dir,
												'format'=>'xml',
												'type_save'=>$type_save,
												'table_cache'=>$table_cache,
												'serialize'=>$serialize
											  );

				$cache = CacheHandler::save($param);

				if($type_save=='database')
				{
					$data = is_object($cache) ? $cache : ArrayToObject($cache);
					return $data->rss;
				}
				else
				{
					return $cache;
				}
			}
			catch(CacheHandlerException $e)
			{
				throw new NewsGoogleFeedException($e->getMessage());
			}
		}
		else
		{
			throw new NewsGoogleFeedException('Invalid News Paramter.');
		}
	}


	/**
	 * Request Data with cURLs method
	 * @param  string query
	 * @return string
	 * @throws NewsGoogleFeedException
	 */
	public static function _RequestData($args=false)
	{
		if(method_exists('cURLs','access_curl'))
		{
			$query = isset($args['query']) ? $args['query'] : '';
			$type_save = isset($args['type_save']) ? $args['type_save'] : 'database';
			$server = 'http://news.google.com/news/section';

			$query = preg_replace('/(\,)/','',$query);
			$query = urlencode('"'.$query.'"');

			$BuildURI = $server.'?q='.$query.'&output=rss';

			$prm = array('url'=>$BuildURI,'type'=>'data');
			$cURLs = new cURLs($prm);
			$data = $cURLs->access_curl();
			
			if(preg_match('/N\:/', $data) > 0)
			{
				return null;
			}
			else
			{
				if($type_save=='database')
				{
					return XML2Array::createArray($data);
				}
				else
				{
					return $data;
				}
			}
		}
		else
		{
			return null;
		}
	}


	/**
	 * Generates better accessible namespaced tags.
	 * @param  SimpleXMLElement
	 * @return void
	 */
	private static function adjustNamespaces($el)
	{
		foreach ($el->getNamespaces(TRUE) as $prefix => $ns)
		{
			$children = $el->children($ns);
			foreach ($children as $tag => $content)
			{
				$el->{$prefix . ':' . $tag} = $content;
			}
		}
	}
}


/**
 * An exception generated by Feed.
 */
class NewsGoogleFeedException extends Exception{}
?>
