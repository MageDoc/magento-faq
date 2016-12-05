<?php

class IssohSystems_Adminhtml_Block_Faq_Category_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function _construct()
    {
        parent::_construct();
        $this->setId('faqCategoryGrid');
        $this->_blockGroup = 'issohadmin';
        $this->_controller = 'faq_category';
    }

    protected function _prepareCollection()
    {
        $store = $this->getRequest()->getParam('store', 0);
        $collection = Mage::getModel('faq/category')->getCollection();

        $collection->getSelect()
                    ->joinLeft(array('category' => 'faq_category_data'), 'category.category_id = main_table.category_id AND category.store_id='. $store, array('name'));

        $collection->setOrder('parent', 'ASC');
        $collection->setOrder('sort_order', 'ASC');

        $this->setCollection($collection);

        $this->setSaveParametersInSession(true);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('category_id', array(
            'header'    => Mage::helper('faq')->__('ID'),
            'width'     => '50px',
            'index'     => 'category_id',
            'type'      => 'number',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('faq')->__('Category'),
            'width'     => '80px',
            'index'     => 'name',
            'type'      => 'text',
            'renderer'  => 'IssohSystems_Adminhtml_Block_Render_Faq_Category_Name',
        ));

        $this->addColumn('parent', array(
            'header'    => Mage::helper('faq')->__('Parent'),
            'width'     => '80px',
            'index'     => 'parent',
            'type'      => 'options',
            'options'   => $this->getParentOptionGridArray(),
        ));

        $this->addColumn('active', array(
            'header'    => Mage::helper('faq')->__('Active'),
            'width'     => '80px',
            'index'     => 'active',
            'type'      => 'options',
            'options'   => Mage::helper('faq')->getActiveOptionArray(),
        ));

        $this->addColumn('level', array(
            'header'    => Mage::helper('faq')->__('Level'),
            'width'     => '80px',
            'index'     => 'level',
            'type'      => 'number',
        ));

        $this->addColumn('sort_order', array(
            'header'    => Mage::helper('faq')->__('Sort Order'),
            'width'     => '80px',
            'index'     => 'sort_order',
            'type'      => 'number',
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('faq')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('faq')->__('Edit'),
                        'url'     => array('base'=> '*/*/edit','params' => array('store' => $this->getRequest()->getParam('store', 0))),
                        'field'   => 'id',
                        'data-column' => 'action',
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    protected function getParentOptionGridArray() {
        $store = $this->getRequest()->getParam('store', 0);
        $collection = Mage::getModel('faq/category')->getCollection();

        $collection->getSelect()
                   ->join(array('category' => 'faq_category_data'), 'category.category_id = main_table.category_id AND level = 0 AND category.store_id='. $store, array('name'));

        $resultArray = array();
        foreach($collection as $category) {
            $resultArray[$category->getCategoryId()] = $category->getName();
        }

        return $resultArray;
    }


    public function getRowUrl($row)
    {
        $url = $this->getUrl('*/faq_category/edit', array('id' => $row->getId(),'store'=>$this->getRequest()->getParam('store', 0)));
        return $url;
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
}