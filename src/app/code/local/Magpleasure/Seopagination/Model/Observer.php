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

class Magpleasure_Seopagination_Model_Observer
{
    /**
     * Helper
     *
     * @return Magpleasure_Seopagination_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('seopagination');
    }


    public function catalogProductViewLoadBefore($event)
    {
        # Class Rewrites
        if ($this->_helper()->confEnabled()){

            # Pager
            $node = Mage::getConfig()->getNode('global/blocks/page/rewrite');
            $xnode = Mage::getConfig()->getNode('global/blocks/page/xrewrite/html_pager');
            $node->appendChild($xnode);

            # Layout Item
            $node = Mage::getConfig()->getNode('global/models/catalog/rewrite');
            $xnode = Mage::getConfig()->getNode('global/models/catalog/xrewrite/layer_filter_item');
            $node->appendChild($xnode);

            if ($this->_helper()->confRelNoFollow()){
                # Toolbar
                $node = Mage::getConfig()->getNode('global/blocks/catalog/rewrite');
                $xnode = Mage::getConfig()->getNode('global/blocks/catalog/xrewrite/product_list_toolbar');
                $node->appendChild($xnode);
            }

        }
    }

    protected function _getPageVarName()
    {
        return Mage::getBlockSingleton('page/html_pager') ? Mage::getBlockSingleton('page/html_pager')->getPageVarName() : 'p';
    }

    public function catalogCategoryLoadAfter($event)
    {
        if ($this->_helper()->confOnlyFirstIsDescribed()){
            $category = $event->getCategory();
            if ((Mage::app()->getRequest()->getModuleName() == "catalog") &&
                (Mage::app()->getRequest()->getControllerName() == "category") &&
                (Mage::app()->getRequest()->getActionName() == "view") &&
                !in_array( Mage::app()->getRequest()->getParam($this->_getPageVarName()), array('1', null))
            ){
                if (Mage::app()->getRequest()->getParam('id') == $category->getId()){
                    $category->setDescription(null);
                    $category->setImage(null);
                }
            }
        }
    }

    public function pagePredispatch($event)
    {
        $seoPaginationFlag = Mage::registry($this->_helper()->getActivityFlag());
        $page = Mage::app()->getRequest()->getParam($this->_getPageVarName());
        $hasPage = !!$page;

        if ($this->_helper()->confSeoPages() && $hasPage && !$seoPaginationFlag){
            $categoryId = Mage::app()->getRequest()->getParam('id');
            if ($categoryId){

                $cacheKey = "MP_SEO_PAGE_REDIRECT_".$categoryId."_".$page;
                $routeUrl = $this->_helper()->getCommon()->getCache()->getPreparedValue($cacheKey);

                if (!$routeUrl){

                    $category = Mage::getModel('catalog/category')->load($categoryId);
                    Mage::register('current_category', $category, true);
                    $params = array_merge(
                        array(
                            '_page' => $page,
                        )
                    );

                    $routeUrl = $this->_helper()
                        ->_url()
                        ->getCategoryUrl($params);

                    $this->_helper()->getCommon()->getCache()->savePreparedValue($cacheKey, $routeUrl);
                }

                Mage::app()->getFrontController()->getResponse()
                    ->setRedirect($routeUrl, 301)
                    ->sendResponse();
                exit;
            }
        }
        return $this;
    }
}