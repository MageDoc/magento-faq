<?php

class IssohSystems_Adminhtml_Block_Faq_Category_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('faq_category_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('faq')->__('FAQ'));
    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label' => Mage::helper('faq')->__('FAQ Category'),
            'title' => Mage::helper('faq')->__('FAQ Category'),
            'content' => $this->getLayout()->createBlock('issohadmin/faq_category_edit_tab_form')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }

    protected function _updateActiveTab()
    {
        $tabId = $this->getRequest()->getParam('tab');
        if( $tabId ) {
            $tabId = preg_replace("#{$this->getId()}_#", '', $tabId);
            if($tabId) {
                $this->setActiveTab($tabId);
            }
        }
    }
}
