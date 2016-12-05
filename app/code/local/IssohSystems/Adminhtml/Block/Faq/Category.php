<?php
class IssohSystems_Adminhtml_Block_Faq_Category extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_blockGroup = 'issohadmin';
        $this->_controller = 'faq_category';
        $this->_headerText = Mage::helper('faq')->__('Manage Category');
        parent::__construct();
    }
}