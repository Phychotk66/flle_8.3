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

require_once(PFAD_ROOT . PFAD_INCLUDES . "tools.Global.php");
require_once dirname(dirname(__FILE__)) . '/init.php';
require_once 'TwintCw/Dispatcher.php';
require_once 'TwintCw/Log.php';

require_once 'Customweb/Licensing/TwintCw/License.php';
Customweb_Licensing_TwintCw_License::run('gpfqjnlftq4vjrbf');
?>
<br />
<div class="category">Informationen</div>
<table class="list">

	<tr>
		<td>TWINT Plugin Version:</td><td>3.0.127</td>
	</tr>
	<tr>
		<td>TWINT Plugin Release Datum:</td><td>Tue, 02 May 2023 15:38:32 +0200</td>
	</tr>
	<tr>
		<td>Cron URL:</td><td><?php echo TwintCw_Util::getUrl('process', 'cron'); ?></td>
	</tr>
</table>
