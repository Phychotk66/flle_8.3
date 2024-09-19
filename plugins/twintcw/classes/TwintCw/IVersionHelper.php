<?php

/**
 * Interface for accessing functions which differ by version
 * 
 * @author sebastian
 *
 */
interface TwintCw_IVersionHelper {
	public function getPluginConfigurations($plugin);
	public function getTranslation($text, $context);
	public function getShopSettings($arr);
	public function writeLog($level, $message, $context);
	public function getTemplateDirectory();
	public function getDb();
	public function convertISO2ISO639($text);
	public function buildScriptTag($source);
	public function getTranslations($plugin);
	public function getPluginKey($plugin);
	
	/**
	 * @return TwintCw_IVersionHelper
	 */
	public static function getInstance();
}