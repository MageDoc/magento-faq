<?php
class IssohSystems_Faq_Block_Items_Detail extends Mage_Core_Block_Template
{
    public function __construct(){
        parent::__construct();

        $store = Mage::app()->getStore()->getId();
        $cid = $this->getRequest()->getParam('cid');
        $id = $this->getRequest()->getParam('id');

        $collection = Mage::getModel('faq/entity')->getCollection()
                                                  ->addFieldToFilter('main_table.faq_id', $id)
                                                  ->addFieldToFilter('main_table.category_id', $cid)
                                                  ->addFieldToFilter('main_table.active', IssohSystems_Faq_Model_Entity::ACTIVE_TRUE);
        $collection->getSelect()
                    ->join(array('data' => 'faq_data'), 'data.faq_id = main_table.faq_id AND data.store_id='. $store, array('question', 'answer', 'related_faq'));

        $this->setItem($collection->getFirstItem());
    }

    protected function _prepareLayout(){
        parent::_prepareLayout();
        return $this;
    }

    protected function getCategory(){
        return Mage::registry('current_category');
    }

    protected function isFeedback($id) {
        $result = false;

        if(Mage::getSingleton('customer/session')->isLoggedIn()){
            $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
            $item = Mage::getModel('faq/feedback')->getItemByContent($id, $customer_id, IssohSystems_Faq_Model_Feedback::CONTENT_OK);
            if ($item && $item->getId()) {
                $result = true;
            }
        } else {
            $feedback = Mage::getSingleton('core/session')->getData('feedback');
            if (isset($feedback[$id])) {
                $result = true;
            }
        }

        return $result;
    }

    protected function getRelatedFaqs($related_faq){
        $store = Mage::app()->getStore()->getId();
        $ids = $result = array();
        if (strlen($related_faq) > 0) {
            $ids = explode(',', $related_faq);
            $collection = Mage::getModel('faq/entity')->getCollection()
                                                      ->addFieldToFilter('main_table.active', IssohSystems_Faq_Model_Entity::ACTIVE_TRUE)
                                                      ->addFieldToFilter('main_table.faq_id', array('in' => $ids));
            $collection->getSelect()->join(array('data' => 'faq_data'), 'data.faq_id = main_table.faq_id AND data.store_id='. $store, array('question'));

            $result = $collection;
        }

        return $result;
    }
}
