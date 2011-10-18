<?php 
class Magento_Cache {
	private static $CACHETIME;
	private static $DEFAULTCACHENAME = 'pronamic-magento-plugin-defaultcache';
	private $currentCacheName;
		
	/**
	 * Constructor of the class, is required to define the
	 * current file name based on the parsed $atts
	 * 
	 * @param mixed array $atts
	 */
	public function __construct($atts, $maxproducts, $cachetime){
		$this->currentCacheName = $this->buildCacheNameFromAtts($atts, $maxproducts);
		self::$CACHETIME = $cachetime;
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
			$tmp = 'magento-' . $tmp . strtolower(trim($maxproducts));
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
			set_transient($this->currentCacheName, $string, self::$CACHETIME);
			set_transient(self::$DEFAULTCACHENAME, '', self::$CACHETIME);
		}catch(Exception $e){	}
	}
}
?>