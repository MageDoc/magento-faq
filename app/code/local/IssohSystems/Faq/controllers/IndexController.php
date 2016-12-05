<?php
class IssohSystems_Faq_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $this->getCurrentCategory();
        $this->loadLayout();
        $this->renderLayout();
    }

    public function detailAction() {
        $this->getCurrentCategory();
        $this->loadLayout();
        $this->renderLayout();
    }

    public function feedbackAction() {
        $cid = $this->getRequest()->getParam('cid');
        $id = $this->getRequest()->getParam('id');

        if (!$cid || !$id) {
            $this->_redirectReferer();
            return;
        }

        if(Mage::getSingleton('customer/session')->isLoggedIn()){
            $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
            $item = Mage::getModel('faq/feedback')->getItemByContent($id, $customer_id, IssohSystems_Faq_Model_Feedback::CONTENT_OK);
            if (!($item && $item->getId())) {
                $model = Mage::getModel('faq/feedback');
                $model->setData('faq_id', $id);
                $model->setData('customer_id', $customer_id);
                $model->setData('content', IssohSystems_Faq_Model_Feedback::CONTENT_OK);
                $model->save();
            }
        } else {
            $session = $this->_getSession();
            $feedback = array();
            if ($session->getData('feedback')) {
                $feedback = $session->getData('feedback');
            }
            $feedback[$id] = array('faq_id' => $id, 'content' => IssohSystems_Faq_Model_Feedback::CONTENT_OK);
            $session->setData('feedback', $feedback);
        }

        $this->_redirect('*/index/detail', array('cid' => $cid, 'id' => $id));
    }

    private function getCurrentCategory() {
        $store = Mage::app()->getStore()->getId();
        $cid = $this->getRequest()->getParam('cid');
        $collection = Mage::getModel('faq/category');
        if ($cid) {
            $collection = $collection->getCollection()
                                     ->addFieldToFilter('active', IssohSystems_Faq_Model_Category::ACTIVE_TRUE)
                                     ->addFieldToFilter('main_table.category_id', $cid);
            $collection->getSelect()
                       ->join(array('category' => 'faq_category_data'), 'category.category_id = main_table.category_id AND category.store_id='. $store, array('name'));

            $collection = $collection->getFirstItem();
        }

        Mage::register('current_category', $collection);
    }

    /**
     * Retrieve core session model object
     *
     * @return Mage_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('core/session');
    }
}
