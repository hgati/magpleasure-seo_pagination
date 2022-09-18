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

class Magpleasure_Common_Block_Adminhtml_Widget_Treeview_Tree extends Magpleasure_Common_Block_Adminhtml_Template
{
    protected $_model;
    protected $_rootCollection;


    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate("magpleasure/treeview/tree.phtml");
    }

    /**
     * @param $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->_model = $model;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->_model;
    }

    public function getJsObjectName()
    {
        return $this->getId()."TreeviewTree";
    }

    public function getHtmlId()
    {
        return strtolower($this->getId())."_tree";
    }

    /**
     * @param $rootCollection
     * @return $this
     */
    public function setRootCollection($rootCollection)
    {
        $this->_rootCollection = $rootCollection;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRootCollection()
    {
        return $this->_rootCollection;
    }

    public function getConfigJson()
    {
        $config = $this->getConfig();
        return $config ? Zend_Json::encode($config) : "{}";
    }

    public function getRootItemsJson()
    {
        $rootItems = $this->getRootItems();
        return $rootItems ? Zend_Json::encode($rootItems) : "[]";
    }

    public function getConfig()
    {
        $loadItemUrl = $this->getUrl(''); ///TODO URL

        return array(
            'url' => $loadItemUrl,
        );
    }

    public function getRootItems()
    {
        $rootItems = array();
        foreach ($this->getRootCollection() as $root){

            /** @var Magpleasure_Common_Model_Treeview_Abstract $root */
            $rootItems[] = array(
                'id' => $root->getId(),
                'label' => $root->getLabel(),
                'has_children' => $root->hasChildren(),
                'position' => $root->getPosition(),
                'loading' => false,
                'children' => $root->getChildrenArray()
            );
        }

        return $rootItems;
    }

    public function getTreeLeafTemplate()
    {
        return $this->prepareAngularJsTemplate('magpleasure/treeview/tree/leaf.phtml');
    }
}

