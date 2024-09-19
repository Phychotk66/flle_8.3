<?php

global $smarty;

if (isset($_REQUEST['twintcw_failed_transaction_id'])) {
	require_once dirname(dirname(__FILE__)) . '/init.php';

	require_once 'TwintCw/Entity/Transaction.php';
	require_once 'TwintCw/Language.php';
	require_once 'Customweb/Payment/Authorization/IErrorMessage.php';
	require_once 'Customweb/Util/Html.php';

	$failedTransaction = TwintCw_Entity_Transaction::loadById($_REQUEST['twintcw_failed_transaction_id']);

	if ($failedTransaction !== null) {
		$errorMessage = current($failedTransaction->getTransactionObject()->getErrorMessages());
		if ($errorMessage instanceof Customweb_Payment_Authorization_IErrorMessage) {
			$errorMessage =  Customweb_Util_Html::convertSpecialCharacterToEntities($errorMessage->getUserMessage());
		}
		if (empty($errorMessage)) {
			$errorMessage = TwintCw_Language::_("Unknown error.");
		}

		$currentMessage = $smarty->get_template_vars('hinweis');
		if (!empty($currentMessage)) {
			$currentMessage .= '<br />';
		}
		$smarty->assign('hinweis', $currentMessage . $errorMessage);

		// For JTL 5.x we need to put the alert into the special facility.
		if (class_exists('Shop', false) && method_exists('Shop', 'Container')) {
			Shop::Container()->getAlertService()->addAlert(Alert::TYPE_ERROR, $errorMessage, 'twintcw');
		}

	}

}
