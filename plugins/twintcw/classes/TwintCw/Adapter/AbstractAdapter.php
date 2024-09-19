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

require_once 'Customweb/Util/Url.php';
require_once 'Customweb/Util/Html.php';

require_once 'TwintCw/Language.php';
require_once 'TwintCw/Util.php';
require_once 'TwintCw/FormRenderer.php';
require_once 'TwintCw/Entity/Transaction.php';
require_once 'TwintCw/Adapter/IAdapter.php';


abstract class TwintCw_Adapter_AbstractAdapter implements TwintCw_Adapter_IAdapter {
	
	/**
	 * @var Customweb_Payment_Authorization_IAdapter
	 */
	private $interfaceAdapter;
	
	/**
	 * @var Customweb_Payment_Authorization_IOrderContext
	 */
	private $orderContext;
	
	/**
	 * @var TwintCw_PaymentMethod
	 */
	protected $paymentMethod;
	
	/**
	 * @var TwintCw_Entity_Transaction
	 */
	protected $failedTransaction = null;
	
	/**
	 * @var TwintCw_Entity_Transaction
	 */
	protected $aliasTransaction = null;
	
	/**
	 * @var int	 
	 */
	private $aliasTransactionId = null;
	
	/**
	 * @var TwintCw_Entity_Transaction
	 */
	private $transaction = null;
	
	/**
	 * @var string
	 */
	private $redirectUrl = null;
	
	public function setInterfaceAdapter(Customweb_Payment_Authorization_IAdapter $interface) {
		$this->interfaceAdapter = $interface;
	}
	
	public function getInterfaceAdapter() {
		return $this->interfaceAdapter;
	}
	
	public function isHeaderRedirectionSupported() {
		require_once 'Customweb/Licensing/TwintCw/License.php';
		$arguments = null;
		return Customweb_Licensing_TwintCw_License::run('ahol7auv0qcns82s', $this, $arguments);
	}

	final public function call_7emvjotgcvg7jfnl() {
		$arguments = func_get_args();
		$method = $arguments[0];
		$call = $arguments[1];
		$parameters = array_slice($arguments, 2);
		if ($call == 's') {
			return call_user_func_array(array(get_class($this), $method), $parameters);
		}
		else {
			return call_user_func_array(array($this, $method), $parameters);
		}
		
		
	}
	
	protected function setRedirectUrl($redirectUrl) {
		$this->redirectUrl = $redirectUrl;
		return $this;
	}
	
	public function getRedirectionUrl() {
		return $this->redirectUrl;
	}
	
	
	public function prepareCheckout(TwintCw_PaymentMethod $paymentMethod, Customweb_Payment_Authorization_IOrderContext $orderContext, $failedTransaction) {
		
		$this->paymentMethod = $paymentMethod;
		$this->failedTransaction = $failedTransaction;
		$this->orderContext = $orderContext;

		$this->aliasTransaction = null;
		$this->aliasTransactionId = null;
		
		if (isset($_REQUEST['twintcw_alias'])) {
			if ($_REQUEST['twintcw_alias'] != 'new') {
				$this->aliasTransaction = TwintCw_Entity_Transaction::loadById((int)$_REQUEST['twintcw_alias']);
			}
			if ($this->aliasTransaction !== null) {
				$this->aliasTransactionId = $this->aliasTransaction->getTransactionId();
			}
			
			if ($_REQUEST['twintcw_alias'] == 'new' || ($this->aliasTransactionId === null && TwintCw_Util::isAliasManagerActive($orderContext))) {
				$this->aliasTransactionId = 'new';
			}
		}
				
		$transaction = $this->getTransaction();
		$this->preparePaymentFormPane();
		if ($transaction->getTransactionObject()->isAuthorizationFailed()) {
			$this->setRedirectUrl(Customweb_Util_Url::appendParameters(
				$transaction->getTransactionObject()->getTransactionContext()->getFailedUrl(),
				$transaction->getTransactionObject()->getTransactionContext()->getCustomParameters()
			));
		}
	}
	
	public function getCheckoutPageHtml() {
		require_once 'Customweb/Licensing/TwintCw/License.php';
		$arguments = null;
		return Customweb_Licensing_TwintCw_License::run('1cu9gt87nk8kj1t0', $this, $arguments);
	}

	final public function call_dspfs8o936cnkd2e() {
		$arguments = func_get_args();
		$method = $arguments[0];
		$call = $arguments[1];
		$parameters = array_slice($arguments, 2);
		if ($call == 's') {
			return call_user_func_array(array(get_class($this), $method), $parameters);
		}
		else {
			return call_user_func_array(array($this, $method), $parameters);
		}
		
		
	}
	
	protected function getAliasDropDown() {
		$orderContext = $this->getOrderContext();
		
		if (!TwintCw_Util::isAliasManagerActive($orderContext)) {
			return '';
		}
		$aliasTransactions =  TwintCw_Util::getAliasHandler()->getAliasTransactions($orderContext);
		if (count($aliasTransactions) <= 0) {
			return '';
		}
		
		$output = '<div class="twintcw-alias-pane"><label for="twintcw_alias">' . TwintCw_Language::_("Use Stored Card") . '</label>';
		
		$output .= '<select name="twintcw_alias" id="twintcw_alias" class="twintcw-alias-dropdown">';
		$output .= '<option value="new">' . TwintCw_Language::_("Use a new Card") . '</option>';
		
		$aliasOptions = array();
		$found = false;
		foreach ($aliasTransactions as $transaction) {
			$selected = $this->aliasTransactionId == $transaction->getTransactionId();
			$aliasOptions[] = array(
				'value' => $transaction->getTransactionId(),
				'display' => $transaction->getAliasForDisplay(),
				'selected' => $selected
			);
			$found = $found || $selected;
		}
		if(!$found && !empty($aliasOptions)) {
			$output = str_replace('option value="new"', 'option value="new" selected', $output);
		}
		foreach($aliasOptions as $aliasOption) {
			$output .= '<option value="' . $aliasOption['value'] . '"';
			if($aliasOption['selected']) {
				$output .= ' selected="selected"';
			}
			$output .= '>' . $aliasOption['display'] . '</option>';
		}
		
		$output .= '</select></div>';
		
		return $output;
	}
	
	protected function getOrderContext() {
		return $this->orderContext;
	}
	
	/**
	 * @return TwintCw_Entity_Transaction
	 */
	private function createNewTransaction() {
		$orderContext = $this->getOrderContext();
		return $this->paymentMethod->newTransaction($this->getOrderContext(), $this->aliasTransactionId, $this->getFailedTransactionObject());
	}
	
	/**
	 * @return TwintCw_Entity_Transaction
	 */
	public function getTransaction() {
		if ($this->transaction === null) {
			$this->transaction = $this->createNewTransaction();
		}
		return $this->transaction;
	}
	
	protected function getAliasTransactionObject() {
		$aliasTransactionObject = null;
		$orderContext = $this->getOrderContext();
		if ($this->aliasTransactionId === 'new') {
			$aliasTransactionObject = 'new';
		}
		if ($this->aliasTransaction !== null && $this->aliasTransaction->getCustomerId() !== null && $this->aliasTransaction->getCustomerId() == $orderContext->getCustomerId()) {
			$aliasTransactionObject = $this->aliasTransaction->getTransactionObject();
		}
		
		return $aliasTransactionObject;
	}
	
	protected function getFailedTransactionObject() {
		$failedTransactionObject = null;
		$orderContext = $this->getOrderContext();
		if ($this->failedTransaction !== null && $this->failedTransaction->getCustomerId() !== null && $this->failedTransaction->getCustomerId() == $orderContext->getCustomerId()) {
			$failedTransactionObject = $this->failedTransaction->getTransactionObject();
		}
		return $failedTransactionObject;
	}
	
	protected function getPaymentFormPane() {
		$output = '<div id="twintcw-checkout-form-pane">';
		
		$actionUrl = $this->getFormActionUrl();
		
		if ($actionUrl !== null && !empty($actionUrl)){
			$output .= '<form action="' . $actionUrl . '" method="POST" class="twintcw-confirmation-form form"  accept-charset="UTF-8"><fieldset class="outer">';
		}
		
		$visibleFormFields = $this->getVisibleFormFields();
		if ($visibleFormFields !== null && count($visibleFormFields) > 0) {
			$renderer = new TwintCw_FormRenderer();
			$renderer->setCssClassPrefix('twintcw-');
			$displayName = $this->getTransaction()->getTransactionObject()->getTransactionContext()->getOrderContext()->getPaymentMethod()->getPaymentMethodDisplayName();
			$output .= '<fieldset><legend>' . $displayName . '</legend>' . $renderer->renderElements($visibleFormFields) . '</fieldset>';
			$output .= '<p class="box_info tright"><em>*</em> ' . TwintCw_Language::_('Mandatory fields') .'</p>';
		}
		
		$hiddenFormFields = $this->getHiddenFormFields();
		if ($hiddenFormFields !== null && count($hiddenFormFields) > 0) {
				$output .= Customweb_Util_Html::buildHiddenInputFields($hiddenFormFields);
		}
		
		$output .= $this->getAdditionalFormHtml();
		
		$output .= $this->getOrderConfirmationButton();
		
		if ($actionUrl !== null && !empty($actionUrl)){
			$output .= '</fieldset></form>';
		}
		
		$output .= '</div>';
		
		return $output;
	}
	
	protected function getAdditionalFormHtml() {
		return '';
	}
	
	/**
	 * Method to load some data before the payment pane is rendered.
	 */
	protected function preparePaymentFormPane() {
		
	}
	
	protected function getVisibleFormFields() {
		return array();
	}
	
	protected function getFormActionUrl() {
		return null;
	}
	
	protected function getHiddenFormFields() {
		return array();
	}
	
	protected function getOrderConfirmationButton() {
		$confirmText = TwintCw_Language::_('Pay');
		
		// We can only go back into the order process, when no order
		// exists.
		$output = '<div class="buttons twintcw-confirmation-buttons tright">';
		$orderId = $this->getTransaction()->getOrderInternalId();
		
		if ($orderId === null ){
			$output .= '<a href="' . TwintCw_Util::getStoreBaseUrl() . '/bestellvorgang.php?editZahlungsart=1" class="submit btn btn-default btn-lg back-button">' . TwintCw_Language::_('Change payment method') . '</a> ';
		}
		
		$output .= '<input type="submit" value="' . $confirmText . '" class="submit btn btn-primary btn-lg pull-right" /></div>';
		
		return $output;
	}
}