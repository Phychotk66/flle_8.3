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

/* @var $transaction TwintCw_Entity_Transaction  */
/* @var $capture Customweb_Payment_Authorization_ITransactionCapture */
/* @var $item Customweb_Payment_Authorization_IInvoiceItem */
?>


<div class="buttons">
	<a class="btn btn-default" href="<?php echo $back; ?>" class="button blue"><?php echo TwintCw_Language::_('Back'); ?> </a>
</div>

<h3><?php echo TwintCw_Language::_('Capture Data'); ?></h3>
<table class="table">
	<tr>
		<td><?php echo TwintCw_Language::_('Capture ID') ?></td>
		<td><?php echo $capture->getCaptureId(); ?></td>
	</tr>

	<tr>
		<td><?php echo TwintCw_Language::_('Capture Date') ?></td>
		<td><?php echo $capture->getCaptureDate()->format($dateFormat); ?></td>
	</tr>
	<tr>
		<td><?php echo TwintCw_Language::_('Capture Amount') ?></td>
		<td><?php echo $capture->getAmount(); ?></td>
	</tr>
	<tr>
		<td><?php echo TwintCw_Language::_('Status') ?></td>
		<td><?php echo $capture->getStatus(); ?></td>
	</tr>
	<?php foreach ($capture->getCaptureLabels() as $label): ?>
	<tr>
		<td><?php echo $label['label'];?> 
		<?php if (isset($label['description'])): ?>
			<img src="../plugins/twintcw/template/images/help.png" alt="<?php echo $label['description']; ?>" title="<?php echo $label['description']; ?>" style="vertical-align:middle; cursor:help;"> 
		<?php endif; ?>
		</td>
		<td><?php echo Customweb_Core_Util_Xml::escape($label['value']);?>
		</td>
	</tr>
	<?php endforeach;?>
</table>

			
<h3><?php echo TwintCw_Language::_('Captured Items'); ?></h3>
<table class="table">
	<thead>
		<tr>
			<th><?php echo TwintCw_Language::_('Name'); ?></th>
			<th><?php echo TwintCw_Language::_('SKU'); ?></th>
			<th><?php echo TwintCw_Language::_('Quantity'); ?></th>
			<th><?php echo TwintCw_Language::_('Tax Rate'); ?></th>
			<th><?php echo TwintCw_Language::_('Total Amount (excl. Tax)'); ?></th>
			<th><?php echo TwintCw_Language::_('Total Amount (incl. Tax)'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($capture->getCaptureItems() as $item):?>
			<tr>
				<td><?php echo $item->getName(); ?></td>
				<td><?php echo $item->getSku(); ?></td>
				<td><?php echo $item->getQuantity(); ?></td>
				<td><?php echo $item->getTaxRate(); ?></td>
				<?php if ($item->getType() == Customweb_Payment_Authorization_IInvoiceItem::TYPE_DISCOUNT):?>
					<td><?php echo Customweb_Util_Currency::formatAmount($item->getAmountExcludingTax() * -1, $transaction->getTransactionObject()->getCurrencyCode()); ?></td>
					<td><?php echo Customweb_Util_Currency::formatAmount($item->getAmountIncludingTax() * -1, $transaction->getTransactionObject()->getCurrencyCode()); ?></td>
				<?php else:?>
					<td><?php echo Customweb_Util_Currency::formatAmount($item->getAmountExcludingTax(), $transaction->getTransactionObject()->getCurrencyCode()); ?></td>
					<td><?php echo Customweb_Util_Currency::formatAmount($item->getAmountIncludingTax(), $transaction->getTransactionObject()->getCurrencyCode()); ?></td>
				<?php endif;?>
								</tr>
		<?php endforeach;?>
	</tbody>
</table>
