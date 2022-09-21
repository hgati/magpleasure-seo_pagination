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
 * @package    Magpleasure_Seopagination
 * @version    1.0.7
 * @copyright  Copyright (c) 2012-2015 MagPleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */

class Magpleasure_Seopagination_Block_Wrapper extends Mage_Core_Block_Template
{
    const CACHE_PREFIX = "mp_sb_p_";

    protected $_productList = null;

    /**
     * Helper
     *
     * @return Magpleasure_Seopagination_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('seopagination');
    }

    protected function _getCachePrefix()
    {
        $parts = array();

        $parts[] = Mage::app()->getStore()->getId();
        $parts[] = $this->_helper()->getCommon()->getRequest()->getCurrentManegtoUrl();

        return self::CACHE_PREFIX. md5(implode("_", $parts)) . "_";
    }

    public function canShow()
    {
        return $this->_helper()->confRelNextPrev();
    }

    /**
     * @return Magpleasure_Seopagination_Block_Catalog_Product_List
     */
    protected function _getFakeProductList()
    {
        if (!$this->_productList){
            $list = Mage::app()->getLayout()->getBlock('product_list');
            if ($list){
                $list->toHtml();
                $this->_productList = $list;
            }
        }
        return $this->_productList;
    }

    /**
     * Pager
     *
     * @return Magpleasure_Seopagination_Block_Page_Html_Pager|boolean
     */
    protected function _getPager()
    {
        $pager = $this->_getFakeProductList()->getToolbarBlock()->getChild('product_list_toolbar_pager');
        if ($pager){
            if ($pager->getCollection()){
                return $pager;
            }
        }
        return false;
    }

    public function getNextUrl()
    {
        $cacheHelper = $this->_helper()->getCommon()->getCache();
        $cacheKey = $this->_getCachePrefix()."next";
        $url = $cacheHelper->getPreparedValue($cacheKey);

        if (is_null($url)){

            if ($pager = $this->_getPager()){
                if (!$pager->isLastPage()){
                    $url = $pager->getNextPageUrl();
                } else {
                    $url = false;
                }
            }

            $cacheHelper->savePreparedValue($cacheKey, $url);
        }

        return $url;
    }

    public function getPreviousUrl()
    {
        $cacheHelper = $this->_helper()->getCommon()->getCache();
        $cacheKey = $this->_getCachePrefix()."prev";
        $url = $cacheHelper->getPreparedValue($cacheKey);

        if (is_null($url)){

            if ($pager = $this->_getPager()){
                if (!$pager->isFirstPage()){
                    $url = $pager->getPreviousPageUrl();
                } else {
                    $url = false;
                }
            }

            $cacheHelper->savePreparedValue($cacheKey, $url);
        }

        return $url;
    }
}