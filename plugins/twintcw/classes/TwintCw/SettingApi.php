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

require_once 'Customweb/Core/Stream/Input/File.php';

require_once 'TwintCw/Language.php';
require_once 'TwintCw/Util.php';
require_once 'TwintCw/VersionHelper.php';


class TwintCw_SettingApi {
	
	private $settingPrefix = '';
	private $paymentMethodMachineName = null;
	private static $settingsArray = null;
	private $pseudoPrefix = '';
	
	/**
	 * @var Plugin
	 */
	private $plugin = null;
	
	/**
	 * 
	 * @param string $paymentMethodMachineName (optional)
	 */
	public function __construct($paymentMethodMachineName = null) {
		$this->paymentMethodMachineName = $paymentMethodMachineName;
		if ($this->paymentMethodMachineName !== null) {
			$moduleId = TwintCw_Util::getPaymentMethodModuleId($this->paymentMethodMachineName);
			$this->settingPrefix = $moduleId . '_';
			$this->pseudoPrefix = TwintCw_Util::getPaymentMethodPseudoSettingPrefix($this->paymentMethodMachineName) . '_';
		}
		$this->plugin = TwintCw_Util::getPluginObject();
		
	}
	
	public function isSettingPresent($key) {
		$definitons = $this->getSettingDefintions();
		if (isset($definitons[$key])) {
			return true;
		}
		else {
			return false;
		}
	}
	
	public function getSettingValue($key, $languageCode = null) {
		
		$definitons = $this->getSettingDefintions();
		
		if (!isset($definitons[$key])) {
			throw new Exception("Could not find setting with key '" . $key . "'.");
		}
		$type = strtolower($definitons[$key]['type']);
		$default = $definitons[$key]['default'];
		
		if ($type == 'file') {
			$value = $this->getRawSettingValue($key);
			$value = trim($value);
			if (!empty($value)) {
				$path = PFAD_ROOT . '/' . ltrim($value, '/');
				if (!file_exists($path)) {
					throw new Exception(TwintCw_Language::_("Could not find file on path @path for setting @setting.", array('@path' => $path, '@setting' => $key)));
				}
				return new Customweb_Core_Stream_Input_File($path);
			}
			else {
				try {
					return TwintCw_Util::getAssetResolver()->resolveAssetStream($default);
				}
				catch(Customweb_Asset_Exception_UnresolvableAssetException $e) {
					return null;
				}
			}
		}
		
		else if ($type == 'multiselect') {
			
			if (!isset($definitons[$key]['options'])) {
				throw new Exception("Could not load the options for multiselect field.");
			}
			
			$values = array();
			$foundAny = false;
			foreach ($definitons[$key]['options'] as $optionKey => $optionText) {
				$optionActive = $this->getRawSettingValue($key . '_' . strtolower($optionKey));
				if ($optionActive !== null) {
					$foundAny = true;
				}
				if ($optionActive == 'active') {
					$values[] = $optionKey;
				}
			}
			
			if ($foundAny) {
				return $values;
			}
			else {
				return explode(',', $default);
			}
		}
		else if ($type == 'multilangfield') {
			if ($languageCode === null) {
				throw new Exception("To query the '" . $key . "' setting,  a language code must be provided.");
			}
			
			$languageCode = (string)$languageCode;
			if (strlen($languageCode) >= 3) {
				$languageCode = strtolower(TwintCw_VersionHelper::getInstance()->convertISO2ISO639($languageCode));
			}
			$key = $key . '_' . $languageCode;
			
			$value = $this->getRawSettingValue($key);
			if ($value !== null) {
				return $value;
			}
			else {
				return $default;
			}
		}
		else {
			$value = $this->getRawSettingValue($key);
			if ($value !== null) {
				return $value;
			}
			else {
				return $default;
			}
		}
	}
	
	/**
	 * This method returns the raw value for the given key. Multi selects, multi lang fields
	 * are not threaded.
	 * 
	 * @param string $key
	 */
	protected function getRawSettingValue($key) {
		$confs = TwintCw_VersionHelper::getInstance()->getPluginConfigurations($this->plugin);

		$key = $this->settingPrefix . $key;
		if (isset($confs[$key])) {
			return utf8_encode($confs[$key]);
		}
		else {
			return null;
		}
	}
	
	public function getSettingDefintions() {
		$array = self::getSettingsArray();
		if ($this->paymentMethodMachineName === null) {
			return $array['global_settings'];
		}
		else {
			return $array[strtolower($this->paymentMethodMachineName)];
		}
	}
	
	private static function getSettingsArray() {
		if (self::$settingsArray === null) {
			self::$settingsArray = self::getSettingsArrayRaw();
		}
		
		return self::$settingsArray;
	}
	
	private static function getSettingsArrayRaw() {
		return array(
		'global_settings' => array(
			'operation_mode' => array(
				'title' => TwintCw_Language::_("Operation Mode"),
 				'description' => TwintCw_Language::_("Used to toggle test live mode The switching from test to live mode will change the used settings from test to live settings"),
 				'type' => 'SELECT',
 				'options' => array(
					'test' => TwintCw_Language::_("Test Mode"),
 					'live' => TwintCw_Language::_("Live Mode"),
 				),
 				'default' => 'test',
 			),
 			'merchant_uuid' => array(
				'title' => TwintCw_Language::_("System ID"),
 				'description' => TwintCw_Language::_("The system id found in the TWINT backend under Sales Outlets System ID"),
 				'type' => 'TEXTFIELD',
 				'default' => '',
 			),
 			'certificate_string' => array(
				'title' => TwintCw_Language::_("Certificate"),
 				'description' => TwintCw_Language::_("Here you can specify which certificate which will be used to encrypt the communication The certificate can be found in the TWINT backend under Settings Order certificate from SwissSign The file must be converted to a PEM file "),
 				'type' => 'TEXTAREA',
 				'default' => '',
 			),
 			'certificate_passphrase' => array(
				'title' => TwintCw_Language::_("Certificate Passphrase"),
 				'description' => TwintCw_Language::_("Here you can set the passphrase for the certificate The passphrase can be retrieved from the ____paymenServiceProviderName____ backend up to five days after the certificate was created If this period has run out a new certificate must be generated"),
 				'type' => 'TEXTFIELD',
 				'default' => '',
 			),
 			'test_merchant_uuid' => array(
				'title' => TwintCw_Language::_("System ID Test"),
 				'description' => TwintCw_Language::_("The system id found in the TWINT backend under Sales Outlets System ID"),
 				'type' => 'TEXTFIELD',
 				'default' => 'ad013fee-a5e8-4de9-ad7d-f3efe0164177',
 			),
 			'test_certificate_string' => array(
				'title' => TwintCw_Language::_("Certificate Test"),
 				'description' => TwintCw_Language::_("Here you can specify which certificate which will be used to encrypt the communication in test mode The certificate can be found in the TWINT backend under Settings Order certificate from SwissSign The file must be converted t"),
 				'type' => 'TEXTAREA',
 				'default' => '-----BEGIN CERTIFICATE-----
MIIEejCCA2KgAwIBAgIBJzANBgkqhkiG9w0BAQsFADBRMQswCQYDVQQGEwJjaDEe
MBwGA1UEChMVQWROb3Z1bSBJbmZvcm1hdGlrIEFHMSIwIAYDVQQDExlUV0lOVCBQ
YXltZW50IEludGVncmF0aW9uMB4XDTE1MDYyNDA2NDE1MloXDTI1MDYyMTA2NDE1
MlowejELMAkGA1UEBhMCY2gxEjAQBgNVBAoTCUN1c3RvbXdlYjEhMB8GA1UEAxMY
VFdJTlQtVGVjaFVzZXIgQ3VzdG9td2ViMTQwMgYKCZImiZPyLGQBARMkMGU3Mjhl
ODgtZGI5Mi00M2E5LWJiZDUtZjNlNDEwYzE1YmEyMIIBIjANBgkqhkiG9w0BAQEF
AAOCAQ8AMIIBCgKCAQEA+mP9MrAPQzQRpB1tG+kA1OTEuMQbF049dL3W1ptbo0O9
tzw/jmmRm8FPesage8hUSsZ/yInIwsKCB/8/ApKm+qOZX4kEw0E8aSpcva3dHmHS
5rr3sNz/ZRk8skyBAlUECpmPKQ/fSMLlZ87rIvW7Y8kodhnL/Y5H+xHmWyhqOMKp
F2wjUUwQkGnf0x3Hv+yYzt5vHOjKUNOyPSuP9AlIc9dQqYMLCB7EXdncCIDQRcwU
EbsZ+IU+SOq9zEmYJ3Ztqpih/RP3hdmcrPHAnjM3O9pMtQg6YuXL06IR7/esjsrj
5RsSIOiyenofeOx3P+3btEVCSE3utxKZyfYuMaanHQIDAQABo4IBMjCCAS4wCQYD
VR0TBAIwADA/BglghkgBhvhCAQ0EMhYwTmV2aXMgS2V5Qm94IEdlbmVyYXRlZCBD
ZXJ0aWZpY2F0ZSB1c2luZyBPcGVuU1NMMAsGA1UdDwQEAwIDqDAdBgNVHSUEFjAU
BggrBgEFBQcDAQYIKwYBBQUHAwIwHQYDVR0OBBYEFOqaltfmWs96xVzJ88wOyDsN
+7Q1MIGBBgNVHSMEejB4gBSr68zVtfOKkp+Z+ErDlcUgrBpR0KFVpFMwUTELMAkG
A1UEBhMCY2gxHjAcBgNVBAoTFUFkTm92dW0gSW5mb3JtYXRpayBBRzEiMCAGA1UE
AxMZVFdJTlQgUGF5bWVudCBJbnRlZ3JhdGlvboIJAOJwo1TeThlPMBEGCWCGSAGG
+EIBAQQEAwIGwDANBgkqhkiG9w0BAQsFAAOCAQEAeQC3F3Ae9nqzt2VlQMnITY0j
fEHzP+dfBCOThdNsZUY1Wy4hqtxmFJ8esWp+h9u+zlqXh2dj0BNT1PNI5bmRV/Xf
vqTNZ7hCnQ1j1y8QaWPAo1koy0jaFRaVJ/u0dSvKtKuQj3Nh2Ba8fdCAlLqNevGc
HIWDTN9Il8+9cFNuh9X2h9H0wLw1pEjTQ5MdbY6JIG7Lqs0VucZS95rT5vMKZw2H
dl6BgzsUQ+m18MakLFUMok0BqW6KVDENpLYTrA+Ff8T4WiOdct1rIpzw5aWh4/ow
iPZcQJ15KoGadVZHFtCUzg7VWFL1B1sQm2M52ydBy1gebLgxvmxsDqXLgAGJLQ==
-----END CERTIFICATE-----
-----BEGIN RSA PRIVATE KEY-----
Proc-Type: 4,ENCRYPTED
DEK-Info: DES-EDE3-CBC,CEBA99B1C56178D0

XHqDIF781fUmHqh954bV4FMZi2NhAaV8HBV2LJBHJ6LVP3VEcFJyai1nzGClOzhM
rKEaW0T1OnBV5LK/EPSNIDRZyruluW0GrubrYA8dT8CWkR+Tg+pvr2SUvNX4vagZ
txBnpv7E97vXGWJokwKqFaJc2meZgq22SBCbc3tqtsFpZNGi2O5/IMEtqvG2txTe
MhQry53O0Ui4Uwxqkg/rXv0as+zkz54C9m/XC/tTWn3o1l+y+KOTKLx4yv02oycX
tS9ALLzxn+HS0WdNlHumBvtcqTO5o9hYepJ5JrBb4KFlCwjtDuHCe2DAdcq2IfaV
nDGgwoHpHmsK5czrjcgiDJBIayQm0N+1o7ye7OgNCeKh4cZLucEoX3I1RpF/9stf
isA9fjHGq11D7YGPca8MM1Upq+n0OHYc1ZIlHt60koRtL3RkfXhvzsvQyCLllm0N
hfyEujK9pwjOQVaCO+CmQvAzUaoQwj7D42QPKbXPmIvz91odE/ByqBUFHzNYJ9gq
qrS9sx6h1vQ4Ghp6VDPW+78N0nZ1Ws9iTr1KGwCTqRUGZ8Mqqgss00jvOoK/xvDy
Ah5q0SRXQqXWsPvOdSoIs9jK9vfAsoSIZyEz5VRaEoSb/Tln8w61rYoPYbFKYW/L
cip92iaIsJJZxpWJAufB7IFs02fQV/ZRPqKULEk75tH7cZYBNhEw+CW9rvyWZ7/i
1MO3Z7SD4haBt93o+T7IDLIgiwTyu/DUbblABI4cJGX+1h0SSj+yvjXdxythFN2R
rttsFfcgKTyMY5b0c/k4RmswtbkVS64WxxSWC4WYiPoxmZe5dZtmd6LHFs2xdnuQ
sicCUl0Ynn25eP3D50h0CNa/IP77SzIWiT6C/mqIJLI8CgrL+TGRI/Hs7LT0EXPG
UZmLN8+7BNNYidPNTmOc+N+Qt3qz2ZxMyQOoA0ZcyMadJr/WhPNQqRM1Tg6SGW1q
vR1twVnnuV7e6L7T0ri72wt9QrWVSS1hEvJiT7ClksP2cvoOBOKHNA7rwTpI4SJh
CB/WxfClWNpjjeB4jgSRJuhQ0AbxFkC4w7KR2PqQbTUycBdL2sbnnhrsmS2ZKLpJ
/V5TrdA/x/LxLlLbvQtFwYqiggcxOiZDqKbZRg1IjQcXnlPDuWVDqy4N62qzlw4m
7FrercyJaOj/YEoIRx15rMO+xDwYblp91bqYH1G6B9HnS9mv3VvznyKtWnMFUvJm
nOYom4Oytlne5pmYn4/I4NcOX+0ymKZ2MRjuLX3FdO5hVnilfqGPw0P6b7amEQ97
8oT1HgGidfDkboQgtYB1oAuggR1B75x30G3tmSvbTNmnbB8zLXV6Zik0TAOqqE1M
HST38OH/+D5kn2ZUKj5cZet5WuqRMXmPtVVO8fwwl2SdiuZNSW5M8jSVba+tEEZ4
CBNl0OFoxkTzunXUR/84bujBqcC1fATfoJWlUoqWIAGGwJUFfYTcfRyPZBG6L5Bq
fuaz5LdN/hC+KrVpMehuj3bzc7Dw6X4rngnlmgAragjaa0L2hFjcbnV1OkWzXm0r
rDHsoYzAp9CYrgthdVI7RQsSO8ujZed6H3WNHmTdhQB4SiWQIhvHeCQA7jvA+xqJ
-----END RSA PRIVATE KEY-----
			',
 			),
 			'test_certificate_passphrase' => array(
				'title' => TwintCw_Language::_("Certificate Passphrase Test"),
 				'description' => TwintCw_Language::_("Here you can set the passphrase for the test certificate The passphrase can be retrieved from the ____paymenServiceProviderName____ backend up to five days after the certificate was created If this period has run out a new certificate must be generated"),
 				'type' => 'TEXTFIELD',
 				'default' => 'XYL5jCxWbwUw8i9mtb5C',
 			),
 			'order_id_schema' => array(
				'title' => TwintCw_Language::_("Order Schema"),
 				'description' => TwintCw_Language::_("Here you can modify what the order number looks like The placeholder id will be replaced with the internal order number eg MyShopid"),
 				'type' => 'TEXTFIELD',
 				'default' => '{id}',
 			),
 			'poll_timeout_server' => array(
				'title' => TwintCw_Language::_("Server Polling Timeout"),
 				'description' => TwintCw_Language::_("The shop polls the transaction status until the transaction is successful The polling timeout indicates the number of minutes the polling is executed before the transaction is cancelled"),
 				'type' => 'TEXTFIELD',
 				'default' => '30',
 			),
 			'log_level' => array(
				'title' => TwintCw_Language::_("Log Level"),
 				'description' => TwintCw_Language::_("Messages of this or a higher level will be logged"),
 				'type' => 'SELECT',
 				'options' => array(
					'error' => TwintCw_Language::_("Error"),
 					'info' => TwintCw_Language::_("Info"),
 					'debug' => TwintCw_Language::_("Debug"),
 				),
 				'default' => 'error',
 			),
 		),
 		'twint' => array(
			'capturing' => array(
				'title' => TwintCw_Language::_("Capturing method"),
 				'description' => TwintCw_Language::_("Should the authorized amount be transferred directly direct or should it be captured manually deferred"),
 				'type' => 'SELECT',
 				'options' => array(
					'deferred' => TwintCw_Language::_("Deferred"),
 					'direct' => TwintCw_Language::_("Direct"),
 				),
 				'default' => 'direct',
 			),
 			'status_authorized' => array(
				'title' => TwintCw_Language::_("Authorized Status"),
 				'description' => TwintCw_Language::_("This status is set when the payment was successfull and it is authorized"),
 				'type' => 'ORDERSTATUSSELECT',
 				'default' => 'authorized',
 			),
 			'status_captured' => array(
				'title' => TwintCw_Language::_("Captured Status"),
 				'description' => TwintCw_Language::_("You can specify the order status for orders that are captured either directly after the order or manually in the backend"),
 				'type' => 'ORDERSTATUSSELECT',
 				'options' => array(
					'no_status_change' => TwintCw_Language::_("Dont change order status"),
 				),
 				'default' => 'no_status_change',
 			),
 			'status_cancelled' => array(
				'title' => TwintCw_Language::_("Cancelled Status"),
 				'description' => TwintCw_Language::_("You can specify the order status when an order is cancelled"),
 				'type' => 'ORDERSTATUSSELECT',
 				'options' => array(
					'no_status_change' => TwintCw_Language::_("Dont change order status"),
 				),
 				'default' => 'cancelled',
 			),
 			'authorizationMethod' => array(
				'title' => TwintCw_Language::_("Authorization Method"),
 				'description' => TwintCw_Language::_("Select the authorization method to use for processing this payment method"),
 				'type' => 'SELECT',
 				'options' => array(
					'PaymentPage' => TwintCw_Language::_("Payment Page"),
 				),
 				'default' => 'PaymentPage',
 			),
 			'payment_receipt' => array(
				'title' => TwintCw_Language::_("Payment Receipts"),
 				'description' => TwintCw_Language::_("This setting controls when the payment receipts are created"),
 				'type' => 'SELECT',
 				'options' => array(
					'authorization' => TwintCw_Language::_("Create the payment receipts during authorization"),
 					'capturing' => TwintCw_Language::_("Create the payment receipts when the transaction is captured"),
 				),
 				'default' => 'authorization',
 			),
 			'error_display_page' => array(
				'title' => TwintCw_Language::_("Error Page"),
 				'description' => TwintCw_Language::_("This setting controls where the error message is shown to the customer"),
 				'type' => 'SELECT',
 				'options' => array(
					'overview' => TwintCw_Language::_("Show the error message on the overview page"),
 					'separte' => TwintCw_Language::_("Show the error message during checkout on a separate page"),
 				),
 				'default' => 'overview',
 			),
 		),
 	);
	}
	
	
}