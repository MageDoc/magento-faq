<?php

class IssohSystems_Adminhtml_Block_Faq_Items_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function _construct()
    {
        parent::_construct();
        $this->setId('faqItemsGrid');
        $this->_blockGroup = 'issohadmin';
        $this->_controller = 'faq_items';
    }

    protected function _prepareCollection()
    {
        $store = $this->getRequest()->getParam('store', 0);
        $collection = Mage::getModel('faq/entity')->getCollection();

        $collection->getSelect()->joinLeft(array('data'=> 'faq_data'), 'data.faq_id = main_table.faq_id AND data.store_id='. $store,
                                           array('question', 'answer', 'created_at', 'updated_at', 'store_id'))
                                ->joinLeft(array('category_entity' => 'faq_category_entity'), 'category_entity.category_id = main_table.category_id', 
                                           array('parent'))
                                ->joinLeft(array('category' => 'faq_category_data'), 'category.category_id = main_table.category_id AND category.store_id='. $store, 
                                           array('name'));

        $collection->setOrder('main_table.category_id', 'asc');
        $collection->setOrder('main_table.sort_order', 'asc');

        Mage::dispatchEvent('faq_item_grid_prepare_collection', array('grid' => $this, 'items' => $collection));

        $this->setCollection($collection);
        $this->setSaveParametersInSession(true);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('faq_id', array(
            'header'    => Mage::helper('faq')->__('ID'),
            'width'     => '50px',
            'index'     => 'faq_id',
            'type'      => 'number',
        ));

        $this->addColumn('question', array(
            'header'    => Mage::helper('faq')->__('Question'),
            'width'     => '200px',
            'index'     => 'question',
            'type'      => 'text',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('faq')->__('Category'),
            'width'     => '80px',
            'index'     => 'name',
            'type'      => 'text',
        ));

        $this->addColumn('type', array(
            'header'    => Mage::helper('faq')->__('Type'),
            'width'     => '80px',
            'index'     => 'parent',
            'type'      => 'options',
            'options'   => $this->toParentOptionArray(),
            'renderer'  => 'IssohSystems_Adminhtml_Block_Render_Faq_Items_Parent_Name',
        ));

        $this->addColumn('active', array(
            'header'        => Mage::helper('faq')->__('Active'),
            'width'         => '70px',
            'index'         => 'active',
            'type'          => 'options',
            'options'       => Mage::helper('faq')->getActiveOptionArray(),
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('faq')->__('Date'),
            'index'     => 'created_at',
            'type'      => 'datetime',
            'width'     => '100px',
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
                        'url'     => array('base'=>'*/*/edit','params' => array('store' => $this->getRequest()->getParam('store', 0))),
                        'field'   => 'id',
                        'data-column' => 'action',
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        Mage::dispatchEvent('faq_item_grid_prepare_columns', array('grid' => $this));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        $url = $this->getUrl('*/faq_items/edit', array('id' => $row->getId(),'store'=>$this->getRequest()->getParam('store', 0)));

        return $url;
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function toParentOptionArray() {
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
}