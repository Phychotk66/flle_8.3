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

require_once 'TwintCw/Controller/Abstract.php';
require_once 'Customweb/Util/System.php';
require_once 'Customweb/Util/Html.php';
require_once 'TwintCw/Cron.php';
require_once 'TwintCw/Util.php';
require_once 'TwintCw/Entity/Transaction.php';
require_once 'TwintCw/Log.php';
require_once 'TwintCw/Adapter/IframeAdapter.php';
require_once 'TwintCw/PaymentMethod.php';
require_once 'TwintCw/Cron.php';
require_once 'TwintCw/Language.php';
require_once 'TwintCw/Adapter/WidgetAdapter.php';
require_once 'TwintCw/Controller/Abstract.php';


require_once 'Customweb/Licensing/TwintCw/License.php';
Customweb_Licensing_TwintCw_License::run('8jarj7qc5jpcp7tc');



