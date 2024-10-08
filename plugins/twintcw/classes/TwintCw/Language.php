<?php 
/**
  * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2018 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
 */

require_once 'Customweb/Util/String.php';
require_once 'Customweb/I18n/Util.php';

class TwintCw_Language {
	
	public static function _($stringToTranslate, $args = array()) {
		
		$key = Customweb_I18n_Util::cleanLanguageKey($stringToTranslate);
		if (strlen($key) > 255) {
			$key = substr($key, 0, 255);
		}
		
		$plugin = TwintCw_Util::getPluginObject();
		$translations = TwintCw_VersionHelper::getInstance()->getTranslations($plugin);

		$translatedString = $stringToTranslate;
		if (isset($translations[$key])) {
			$translatedString = utf8_encode($translations[$key]);
		}

		$text = Customweb_Util_String::formatString($translatedString, $args);

		return is_string($text) && !empty($text) ? UConverter::transcode($text, 'ISO-8859-1', 'UTF-8') : $text;
	}
	
}