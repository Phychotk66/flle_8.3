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

require_once("includes/globalinclude.php");
$AktuelleSeite = "TWINTCW";

//session starten
$session = new Session();

//erstelle $smarty
if (file_exists(PFAD_INCLUDES."smartyInclude.php")) {
	require_once(PFAD_INCLUDES."smartyInclude.php");
}
else {
	require_once(PFAD_INCLUDES."smartyInlcude.php");
}

require_once __DIR__ . '/plugins/twintcw/classes/TwintCw/VersionHelper.php';

//einstellungen holen
$Einstellungen = TwintCw_VersionHelper::getInstance()->getShopSettings(array(CONF_GLOBAL));
pruefeHttps();


//hole alle OberKategorien
$AlleOberkategorien = new KategorieListe();
$AlleOberkategorien->getAllCategoriesOnLevel(0);
$AktuelleKategorie = new Kategorie(verifyGPCDataInteger("kategorie"));
$AufgeklappteKategorien = new KategorieListe();
$AufgeklappteKategorien->getOpenCategories($AktuelleKategorie);
$startKat = new Kategorie();
$startKat->kKategorie=0;


//spezifische assigns
$smarty->assign('requestURL', $requestURL);
$smarty->assign('Einstellungen', $Einstellungen);

require_once dirname(__FILE__) . '/plugins/twintcw/init.php';
require_once 'TwintCw/Dispatcher.php';
require_once 'TwintCw/Log.php';
require_once 'Customweb/Payment/Endpoint/Dispatcher.php';


try {
	header_remove('set-cookie');
	if (isset($_REQUEST['c'])) {
		$dispatcher = new Customweb_Payment_Endpoint_Dispatcher(TwintCw_Util::getEndpointAdapter(), TwintCw_Util::getContainer(), array(
			0 => 'Customweb_Twint',
 			1 => 'Customweb_Payment_Authorization',
 		));
		$request = Customweb_Core_Http_ContextRequest::getInstance();
		$response = new Customweb_Core_Http_Response($dispatcher->dispatch($request));
		$response->send();
		die();
	}
	else {
		$dispatcher = new TwintCw_Dispatcher();
		$dispatcher->dispatch();
	}
}
catch(Exception $e) {
	TwintCw_Log::add($e->getMessage() . "\n\n" . $e->getTraceAsString());
	echo '<pre>';
	echo $e->getMessage();
	echo "\n\n";
	echo $e->getTraceAsString();
}

