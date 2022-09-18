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

class Magpleasure_Seopagination_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
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

    public function initControllerRouters($observer)
    {
        $front = $observer->getEvent()->getFront();

        $router = new Magpleasure_Seopagination_Controller_Router();
        $front->addRouter('seopagination', $router);
    }

    protected function _getPageVarName()
    {
        return Mage::getBlockSingleton('page/html_pager') ? Mage::getBlockSingleton('page/html_pager')->getPageVarName() : 'p';
    }

    public function match(Zend_Controller_Request_Http $request)
    {
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }

        if (!$this->_helper()->confSeoPages()) {
            return false;
        }

        $identifier = $request->getPathInfo();
        if ($identifier && ($identifier[0] == "/")) {
            $identifier = substr($identifier, 1, strlen($identifier));
        }

        /** @var $urlModel Magpleasure_Seopagination_Model_Url */
        $urlModel = $this->_helper()->_url();
        $page = $urlModel->responsePage($identifier);
        $pageVarName = $this->_getPageVarName();

        if ($this->_helper()->getCommon()->getMagento()->getModuleVersion('Magentix_RewritingFilters')) {

            if ($page && ($page > 1)) {

                $request->setPathInfo(str_replace("/" . $page, "", $request->getPathInfo()));
                $request->setParam('p', $page);

                $router = new Magentix_RewritingFilters_Controller_Router();
                if ($router->match($request)) {
                    return true;
                }
            }
        }

        $realUrl = $urlModel->responseUrl($identifier, $page);

        if ($this->_helper()->getCommon()->getMagento()->isModuleEnabled("Enterprise_UrlRewrite")){

            /** @var $urlRewrite Enterprise_UrlRewrite_Model_Redirect */
            $urlRewrite = Mage::getModel('enterprise_urlrewrite/redirect');
            $urlRewrite
                ->loadByRequestPath($realUrl, Mage::app()->getStore()->getId());

        } else {

            $urlRewrite = Mage::getModel('core/url_rewrite');
            $urlRewrite
                ->setStoreId(Mage::app()->getStore()->getId())
                ->loadByRequestPath($realUrl);
        }

        if ($urlRewrite->getId() && !Mage::registry($this->_helper()->getActivityFlag())) {

            $request->setPathInfo($urlRewrite->getTargetPath());
            if ($page) {
                $request->setParam($pageVarName, $page);
            }
            Mage::register($this->_helper()->getActivityFlag(), true, true);
            $request->setAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, $identifier);
            return true;

        } else {

            $categoryId = $urlModel->getCategoryIdByUrl($realUrl);
            if ($page && $categoryId) {

                $request
                    ->setModuleName('catalog')
                    ->setControllerName('category')
                    ->setActionName('view')
                    ->setAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, $identifier)
                    ->setParam('id', $categoryId)
                    ->setParam($pageVarName, $page)
                ;

                Mage::register($this->_helper()->getActivityFlag(), true, true);
                return true;
            }
        }

        return false;
    }

}
