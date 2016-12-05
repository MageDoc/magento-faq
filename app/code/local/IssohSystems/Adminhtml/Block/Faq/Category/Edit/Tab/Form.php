<?php

class IssohSystems_Adminhtml_Block_Faq_Category_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct(){
        parent::__construct();
        $this->setId('faq_category_form');
        $this->setTemplate('faq/category/edit/tab/form.phtml');
    }

    public function getParentHtml() {
        $category = $this->getCategory();
        $options = $this->toParentOptionArray();
        $html = $this->getLayout()->createBlock('core/html_select')
                        ->setOptions($options)
                        ->setValue($category->getParent())
                        ->setName('parent')
                        ->setId('parent')
                        ->getHtml();

        return $html;
    }

    public function getActiveHtml() {
        $category = $this->getCategory();
        $options = $this->toActiveOptionArray();
        $html = $this->getLayout()->createBlock('core/html_select')
                        ->setOptions($options)
                        ->setValue($category->getActive())
                        ->setName('active')
                        ->setId('active')
                        ->getHtml();

        return $html;
    }

    public function getIconHtml() {
        $category = $this->getCategory();
        $options = $this->toIconOptionArray();
        $html = $this->getLayout()->createBlock('core/html_select')
                        ->setOptions($options)
                        ->setValue($category->getIconClass())
                        ->setName('icon_class')
                        ->setId('icon_class')
                        ->getHtml();

        return $html;
    }

    public function getCategory() {
        $category = Mage::getModel('faq/category');
        if (Mage::registry('current_category')) {
            $category = Mage::registry('current_category');
        }
        return $category;
    }

    protected function toParentOptionArray() {
        $store = Mage::registry('store_id');
        $collection = Mage::getModel('faq/category')->getCollection();

        $collection->getSelect()
                   ->join(array('category' => 'faq_category_data'), 'category.category_id = main_table.category_id AND level = 0 AND category.store_id='. $store, array('name'));

        $resultArray = array();
        foreach($collection as $category) {
            $resultArray[] = array('value' => $category->getCategoryId(), 'label' => $category->getName());
        }
        $resultArray[] = array('value' => IssohSystems_Faq_Model_Category::PARENT_CATEGORY_ID, 'label' => 'top level');

        return $resultArray;
    }

    protected function toActiveOptionArray()
    {
        $options = Mage::helper('faq')->getActiveOptionArray();
        $result = array();
        foreach($options as $key => $value) {
            $result[] = array('value' => $key, 'label' => $value);
        }
        return $result;
    }

    protected function toIconOptionArray() {
        $icons = Mage::helper('faq')->getFontAawesome();
/*
        $keys = array_keys($icons);
        shuffle($keys);

        $r = array();
        for ($i = 0; $i < 200; $i++) {
            $r[$keys[$i]] = $arr[$keys[$i]];
        }
*/
        $result = array();
        foreach($icons as $key => $value) {
            $result[] = array('value' => $key, 'label' => sprintf('<span class="fa %s" style="font-size:15px; margin-right:10px; margin-bottom:10px;"></span>', $key));
        }
        return $result;
    }
}
