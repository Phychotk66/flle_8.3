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

require_once 'Customweb/Twint/Stubs/Ch/Twint/Service/Base/Types/V2/MerchantInformationType.php';
/**
 * @XmlType(name="CheckSystemStatusRequestType", namespace="http://service.twint.ch/merchant/types/v2")
 */ 
class Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CheckSystemStatusRequestType {
	/**
	 * @XmlElement(name="MerchantInformation", type="Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType", namespace="http://service.twint.ch/merchant/types/v2")
	 * @var Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType
	 */
	private $merchantInformation;
	
	public function __construct() {
	}
	
	/**
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CheckSystemStatusRequestType
	 */
	public static function _() {
		$i = new Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CheckSystemStatusRequestType();
		return $i;
	}
	/**
	 * Returns the value for the property merchantInformation.
	 * 
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType
	 */
	public function getMerchantInformation(){
		return $this->merchantInformation;
	}
	
	/**
	 * Sets the value for the property merchantInformation.
	 * 
	 * @param Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType $merchantInformation
	 * @return Customweb_Twint_Stubs_Ch_Twint_Service_Merchant_Types_V2_CheckSystemStatusRequestType
	 */
	public function setMerchantInformation($merchantInformation){
		if ($merchantInformation instanceof Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType) {
			$this->merchantInformation = $merchantInformation;
		}
		else {
			throw new BadMethodCallException("Type of argument merchantInformation must be Customweb_Twint_Stubs_Ch_Twint_Service_Base_Types_V2_MerchantInformationType.");
		}
		return $this;
	}
	
	
	
}