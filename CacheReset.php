<?php 
	// Calls the setReset() function in CacheClass, forces the cache to renew on it's next run.
	include_once('CacheClass.php');
	CacheClass::setReset();
?>