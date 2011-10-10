<?php 
class CacheClass {
	private static $DEFAULTCACHENAME = 'pronamic-magento-plugin-defaultcache';
	private $currentCacheName;
		
	/**
	 * Constructor of the class, is required to define the
	 * current file name based on the parsed $atts
	 * 
	 * @param mixed array $atts
	 */
	public function __construct($atts, $maxproducts){
		$this->currentCacheName = $this->buildCacheNameFromAtts($atts, $maxproducts);
	}
	
	/**
	 * Reads the cache file and return it in a string
	 * 
	 * @return String $cache
	 * @throws Exception $e When the cache is forced to be renewed or when no cache is found
	 */
	public function getCache(){
		if(get_transient(self::$DEFAULTCACHENAME) == 'RESET'){
			throw new Exception('Settings changed, cache forced to be renewed.');
		}
		$cache = '';
		$cache = get_transient($this->currentCacheName);
		if(empty($cache)){
			throw new Exception('Cache not found');
		}
		return $cache;
	}
	
	/**
	 * Takes the atts array and turns it into a string which represents the 
	 * cache file name. This makes it easy to alocate the right cachefile
	 * when variable information is required.
	 * 
	 * @param mixed array $atts
	 * @return String $tmp 
	 */
	private function buildCacheNameFromAtts($atts, $maxproducts){
		$tmp = '';
		foreach($atts as $key=>$value){
			$key = strtolower(trim($key));
			$tmp .= $key;
			$value = explode(',', $value);
			foreach($value as $value2){
				$value2 = strtolower(trim($value2));
				$tmp .= $value2;
			}
		}
		if(!empty($tmp)){
			$tmp .= strtolower(trim($maxproducts));
		}else{
			$tmp .= self::$DEFAULTCACHENAME;
		}
		
		return $tmp;
	}
	
	/**
	 * Deletes and then writes to the current cache file.
	 * 
	 * @param String $string
	 */
	public function storeCache($string){
		try{
			$time = '';
			$time = get_option('magento-caching-time');
			if(empty($time)){
				$time = 3600;
			}
			
			set_transient($this->currentCacheName, $string, $time);
			set_transient(self::$DEFAULTCACHENAME, '', $time);
		}catch(Exception $e){	}
	}
	
	/**
	 * Sets the default cachename's value to RESET, this will result in a thrown exception
	 * in getCache() so all cache will be resetted and renewed. This comes in handy when a
	 * new caching time is set in the settings.
	 */
	public static function setReset(){
		try{
			$time = '';
			$time = get_option('magento-caching-time');
			if(empty($time)){
				$time = 3600;
			}
			set_transient(self::$DEFAULTCACHENAME, 'RESET', $time);
		}catch(Exception $e){	}
	}
}
?>