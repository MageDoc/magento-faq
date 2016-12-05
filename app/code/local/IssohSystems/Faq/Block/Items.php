<?php
class IssohSystems_Faq_Block_Items extends Mage_Core_Block_Template
{
    public function __construct(){
        parent::__construct();

        $store = Mage::app()->getStore()->getId();
        $cid = $this->getRequest()->getParam('cid');
        $keyword = $this->getRequest()->getParam('q');
        $collection = Mage::getModel('faq/entity')->getCollection()
                                                  ->addFieldToFilter('active', IssohSystems_Faq_Model_Entity::ACTIVE_TRUE);

        if ($cid) {
            $collection->addFieldToFilter('main_table.category_id', $cid);
        }

        $collection->getSelect()
                    ->join(array('data' => 'faq_data'), 'data.faq_id = main_table.faq_id AND data.store_id='. $store, array('question'));

        if ($keyword) {
            $collection->addFieldToFilter('data.question', array('like' => "%$keyword%"));
            $this->setKeyword($keyword);
        }

        $collection->setOrder('category_id', 'ASC');
        $collection->setOrder('sort_order', 'ASC');

        $this->setCollection($collection);
    }

    protected function _prepareLayout(){
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'faq.list.pager')
            ->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
        return $this;
    }

    protected function getCategory(){
        return Mage::registry('current_category');
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
