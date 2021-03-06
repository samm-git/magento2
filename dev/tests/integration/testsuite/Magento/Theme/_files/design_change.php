<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

$storeId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
    'Magento\Framework\Store\StoreManagerInterface'
)->getDefaultStoreView()->getId();
/** @var $change \Magento\Theme\Model\Design */
$change = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Theme\Model\Design');
$change->setStoreId($storeId)->setDesign('Magento/luma')->setDateFrom('2001-01-01 01:01:01')->save();
