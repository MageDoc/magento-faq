<?php
class IssohSystems_Adminhtml_Block_Faq_Items extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_blockGroup = 'issohadmin';
        $this->_controller = 'faq_items';
        $this->_headerText = Mage::helper('faq')->__('Manage FAQ');
        parent::__construct();
    }
}