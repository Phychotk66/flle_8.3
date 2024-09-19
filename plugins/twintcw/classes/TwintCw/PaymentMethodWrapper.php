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

require_once 'Customweb/Payment/Authorization/IPaymentMethod.php';
require_once 'TwintCw/Util.php';
require_once 'TwintCw/PaymentMethod.php';

class TwintCw_PaymentMethodWrapper implements Customweb_Payment_Authorization_IPaymentMethod {
	
	private $machineName = "";
	private $frontendName = "";
	
	/**
	 * @var TwintCw_PaymentMethod
	 */
	private $method = null;
	
	public function __construct(TwintCw_PaymentMethod $method) {
		$this->machineName = $method->getPaymentMethodName();
		$this->method = $method;
		$this->frontendName = $method->getPaymentMethodDisplayName();
	}
	
	public function getPaymentMethodName() {
		if ($this->getMethod() != null) {
			return $this->getMethod()->getPaymentMethodName();
		}
		else {
			return $this->machineName;
		}
	}
	
	public function getPaymentMethodDisplayName() {
		if ($this->getMethod() != null) {
			return $this->getMethod()->getPaymentMethodDisplayName();
		}
		else {
			return $this->frontendName;
		}
	}
	
	public function getPaymentMethodConfigurationValue($key, $languageCode = null) {
		if ($this->getMethod() != null) {
			return $this->getMethod()->getPaymentMethodConfigurationValue($key, $languageCode);
		}
		else {
			return "";
		}
	}
	
	public function existsPaymentMethodConfigurationValue($key, $languageCode = null) {
		if ($this->getMethod() != null) {
			return $this->getMethod()->existsPaymentMethodConfigurationValue($key, $languageCode);
		}
		else {
			return false;
		}
	}
	
	public function __sleep() {
		return array('machineName', 'frontendName');
	}
	
	public function __wakeup() {
	}
	
	/**
	 * @return  TwintCw_PaymentMethod
	 */
	protected function getMethod() {
		if ($this->method === null) {
			$this->method = TwintCw_PaymentMethod::getInstanceByPaymentMethodName($this->machineName);
		}
		return $this->method;
	}
	
}
	