<?php

class IssohSystems_Adminhtml_Block_Faq_Items_Edit_Tab_Related extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('relatedGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('faq_id');
        $this->setDefaultFilter(array('related_faq' => 1));
        $this->setDefaultDir('DESC');
        //$this->setSaveParametersInSession(false);
    }

    protected function _addColumnFilterToCollection($column) {
        if ($column->getId() == 'related_faq') {

            $ids = $this->_getSelectedFaqs();
            if (empty($ids)) {
                $ids = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('faq_id', array('in' => $ids));
            } elseif (!empty($ids)) {
                $this->getCollection()->addFieldToFilter('faq_id', array('nin' => $ids));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection() {
        $ids = $this->_getSelectedFaqs();
        $store = $this->getRequest()->getParam('store', 0);
        $collection = Mage::getModel('faq/data')->getCollection()
                                                ->addFieldToSelect('question')
                                                ->addFieldToSelect('faq_id')
                                                ->addFieldToFilter('faq_id' , array("neq" => $this->getRequest()->getParam('id')))
                                                ->addFieldToFilter('store_id' , $store);
        $this->setCollection($collection);

        if (!Mage::registry('faq_data')) {
            $ids = $this->_getSelectedFaqs();
            if (empty($ids)) {
                $ids = 0;
            }
            $collection->addFieldToFilter('faq_id', array('in' => $ids));
        }

        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('related_faq_ck', array(
                'header_css_class' => 'a-center',
                'type'             => 'checkbox',
                'name'             => 'related_faq[]',
                'values'           => $this->_getSelectedFaqs(),
                'align'            => 'center',
                'index'            => 'faq_id',
                'field_name'       => 'related_faq[]', 
            ));

        $this->addColumn('related_faq_id', array(
                'header'    => Mage::helper('faq')->__('ID'),
                'sortable'  => true,
                'width'     => '60px',
                'index'     => 'faq_id'
            ));

        $this->addColumn('related_question', array(
                'header' => Mage::helper('faq')->__('Question'),
                'index' => 'question',
                'sortable' => true
            ));

        return parent::_prepareColumns();
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    protected function _getFaqs() {
        return Mage::registry('faq_data');
    }

    protected function _getSelectedFaqs() {
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        $faqs = $this->getRequest()->getPost('related_faq');
        $related_faqs = Mage::getModel('faq/data')->getCollection()
                                                  ->addFieldToFilter('faq_id', (int) $this->getRequest()->getParam('id'))
                                                  ->addFieldToFilter('store_id', (int) $this->getRequest()->getParam('store'))
                                                  ->getFirstItem()
                                                  ->getData();

        $sel_faqs = $related_faqs
            ? explode(",", $related_faqs['related_faq'])
            : array();
        if (!is_null($faqs)) {
            $sel_faqs = array_merge($faqs, $sel_faqs);
        }
        return $sel_faqs;
    }

    public function getRelFaqs() {
        return $this->_getSelectedFaqs();
    }
}
