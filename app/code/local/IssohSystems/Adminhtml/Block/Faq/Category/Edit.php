<?php

class IssohSystems_Adminhtml_Block_Faq_Category_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct() {
        parent::__construct();

        $this->_objectId = 'faq_category_edit_id';
        $this->_blockGroup = 'issohadmin';
        $this->setId('faqCategoryEdit');
        $this->_controller = 'faq_category';
    }

    protected function _prepareLayout() {
        parent::_prepareLayout();
    }

    public function getHeaderText() {
        if( Mage::registry('faq_category') && Mage::registry('faq_category')->getId() ) {
            return Mage::helper('faq')->__("Edit Category '%s'", $this->htmlEscape(Mage::registry('faq_category')->getName()));
        } else {
            return Mage::helper('faq')->__('Add Category');
        }
    } 
}