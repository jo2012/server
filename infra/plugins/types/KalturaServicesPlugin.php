<?php
interface KalturaServicesPlugin
{
	/**
	 * @return array<string,string> in the form array[serviceName] = serviceClass
	 */
	public static function getServicesMap();
	
	/**
	 * @return string - the path to services.ct
	 */
	public static function getServiceConfig();	
}