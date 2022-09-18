<?php
/**
 * MagPleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE-CE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE-CE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * MagPleasure does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Magpleasure does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   MagPleasure
 * @package    Magpleasure_Common
 * @version    0.8.0
 * @copyright  Copyright (c) 2012-2014 MagPleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */

class Magpleasure_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Sales_Order
    extends Magpleasure_Common_Block_Adminhtml_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $orderId = $this->_getValue($row);
        if ($orderId) {
            $html = "";
            /** @var Mage_Sales_Model_Order $order  */
            $order = Mage::getModel('sales/order')->load($orderId);
            $incrementId = $order->getIncrementId();
            $url = $this->getUrl('adminhtml/sales_order/view', array('order_id'=>$orderId));
            $html .= "<a href=\"{$url}\" target=\"_blank\">#{$incrementId}</a>";
            return $html;
        } else {
            return $this->_commonHelper()->__("No order");
        }
    }
}