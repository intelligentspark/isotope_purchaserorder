<?php

/**
 * Isotope eCommerce for Contao Open Source CMS
 *
 * Copyright (C) 2009-2014 terminal42 gmbh & Isotope eCommerce Workgroup
 *
 * @package    Isotope
 * @link       http://isotopeecommerce.org
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */



/**
 * Payment methods
 */
\Isotope\Model\Payment::registerModelType('purchaseorder', 'IntelligentSpark\Model\Payment\PurchaseOrder');