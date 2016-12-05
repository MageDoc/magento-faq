<?php

class IssohSystems_Adminhtml_Faq_ItemsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->loadLayout();

        /**
         * Set active menu item
         */
        $this->_setActiveMenu('faq/items');

        /**
         * Append customers block to content
         */
        $this->_addContent(
            $this->getLayout()->createBlock('issohadmin/faq_items')
        );
        $this->renderLayout();
    }

    public function newAction() {
        Mage::register('faq_new_action', true);
        $this->_forward('edit');
    }

    public function editAction() {

        $store = $this->getRequest()->getParam('store', 0);
        $id = $this->getRequest()->getParam('id');

        $collection = Mage::getModel('faq/entity')->getCollection()->addFieldToFilter('main_table.faq_id', $id);
        $collection->getSelect()->joinLeft(array('data'=> 'faq_data'), 'data.faq_id = main_table.faq_id AND data.store_id='. $store, array('question', 'answer', 'store_id'));
        $model = $collection ->getFirstItem();

        if ($model && $model->getId()){
            $model =  $model->setActive($model->getActive())
                            ->setStoreId($model->getStoreId())
                            ->setQuestion($model->getQuestion())
                            ->setAnswer($model->getAnswer());
        }

        if ($model->getId() || Mage::registry('faq_new_action') || is_null($id)){
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('store_id', $store);
            Mage::register('current_faq', $model);

            $this->loadLayout();
            $this->_setActiveMenu('faq/items');

            $this->_addContent($this->getLayout()->createBlock('issohadmin/faq_items_edit'))
                 ->_addLeft($this->getLayout()->createBlock('adminhtml/store_switcher'))
                 ->_addLeft($this->getLayout()->createBlock('issohadmin/faq_items_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('faq')->__('Items does not exist'));
            $this->_redirect('*/*/', array('store' => $store));
        }
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {

            $model = Mage::getModel('faq/entity');
            $model_text = Mage::getModel('faq/data');

            try {
                $editFlag = false;
                $defaultStoreId = intval(Mage::helper('faq')->getDefaultStoreId());
                $faq_id = $this->getRequest()->getParam('id');
                if ($faq_id) {
                    $model = $model->load($faq_id);
                    if (!$model->getId()) {
                        $this->_redirect('*/*/');
                    }
                    $editFlag = true;
                }

                $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
                $connection->beginTransaction();

                $model->setActive($data['active']);
                $model->setCategoryId($data['category_id']);
                $model->setSortOrder($data['sort_order']);

                $this->updateSortOrder($connection, $model, $data['sort_order']);

                $model->save();

                $faq_id = $model->getId();
                $data['faq_id'] = $faq_id;
                $data['store_id'] = intval($data['store_id']);

                if (is_array($data['related_faq'])) {
                    $data['related_faq'] = implode(",", array_unique($data['related_faq']));
                }

                // edit
                if ($editFlag) {
                    if ($data['store_id'] == 0) {
                        $text_data = $this->_initSaveFaq(0, $data);
                        $text_data->save();

                        foreach (Mage::app()->getWebsites() as $website) {
                            foreach ($website->getGroups() as $group) {
                                $stores = $group->getStores();
                                foreach ($stores as $store) {
                                    if ($defaultStoreId === intval($store->getId())) {
                                        $text_data = $this->_initSaveFaq($store->getId(), $data);
                                        $text_data->save();
                                    }
                                }
                            }
                        }

                    } else {
                        $text_data = $this->_initSaveFaq($data['store_id'], $data);
                        $text_data->save();
                    }

                // new faq
                } else {
                    $text_data = $this->_initSaveFaq(0, $data);
                    $text_data->save();

                    foreach (Mage::app()->getWebsites() as $website) {
                        foreach ($website->getGroups() as $group) {
                            $stores = $group->getStores();
                            foreach ($stores as $store) {
                                $text_data = $this->_initSaveFaq($store->getId(), $data);
                                $text_data->save();
                            }
                        }
                    }
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('faq')->__('FAQ was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                $connection->commit();

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $faq_id, 'store' => $data['store_id']));
                    return;
                }
                $this->_redirect('*/*/', array('store' => $data['store_id']));
                return;
            } catch (Exception $e) {
                $connection->rollback();
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id'), 'store' => $data['store_id']));
                return;
            }
        }

        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('faq')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if (($id = $this->getRequest()->getParam('id')) > 0) {
            try {
                $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
                $connection->beginTransaction();

                $model = Mage::getModel('faq/entity')->load($id);
                $sql = "update faq_entity set sort_order = sort_order - 1 where category_id = ? and sort_order > ? ";
                $params = array($model->getCategoryId(), $model->getSortOrder());
                $connection->query($sql, $params);

                $where = $connection->quoteInto('faq_id=?', $id);
                $table = Mage::getModel('faq/entity')->getResource()->getMainTable();
                $connection->delete($table, $where);

                $table = Mage::getModel('faq/data')->getResource()->getMainTable();
                $connection->delete($table, $where);

                $connection->commit();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                $connection->rollback();
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    private function _initSaveFaq($store_id, $data) {
        $text_data = Mage::getModel('faq/data')->getDataByStore($data['faq_id'], $store_id);
        $text_data->setStoreId($store_id);
        $text_data->setFaqId($data['faq_id']);
        $text_data->setCategoryId($data['category_id']);
        $text_data->setQuestion($data['question']);
        $text_data->setAnswer($data['answer']);
        $text_data->setRelatedFaq($data['related_faq']);
        return $text_data;
    }

    protected function _initFaq($getRootInstead = false) {
        $id = (int) $this->getRequest()->getParam('id', false);
        $faq = Mage::getModel('faq/entity');

        if ($id) {
            $faq->load($id);
        }
        Mage::register('faq_data', $faq);
        Mage::register('current_faq', $faq);
        return $faq;
    }

    public function gridAction() {

        if (!$faq = $this->_initFaq(true)) {
            return;
        }

        $this->loadLayout();
        $faq = $this->getRequest()->getPost('related_faq');
        $related_faq = Mage::getModel('faq/data')->getCollection()
                                                 ->addFieldToFilter('faq_id', (int) $this->getRequest()->getParam('id'))
                                                 ->addFieldToFilter('store_id', (int) $this->getRequest()->getParam('store'))
                                                 ->getFirstItem()
                                                 ->getData();

        $sel_faqs = explode(",", $related_faq['related_faq']);
        if (!is_null($faq)) {
            $sel_faqs = array_merge($faq, $sel_faqs);
        }

        $this->getLayout()->getBlock('related.grid')->setRelatedFaq($sel_faqs);
        $this->renderLayout();
    }

    public function relatedAction() {

        $this->loadLayout();
        $this->getLayout()->getBlock('related.grid')->setRelatedFaq($this->getRequest()->getPost('related_faq', null));
        $this->renderLayout();
    }

    public function sortorderAction() {
        $response = array();
        $response['sort_order'] = 1;
        if (!is_null($pid = $this->getRequest()->getPost('category_id'))) {
            $collection =  Mage::getModel('faq/entity')->getCollection()->addFieldToFilter('category_id', $pid);
            $response['sort_order'] = $collection->getSize();
            $response['sort_order'] += 1;
        }

        $this->getResponse()->setBody(json_encode($response));
    }


    private function updateSortOrder($conn, $model, $sortOrder) {

        // new
        if (!$model->getId()) {
            $sql = "update faq_entity set sort_order = sort_order + 1 where category_id = ? and sort_order >= ? ";
            $params = array($model->getCategoryId(), $sortOrder);
            $conn->query($sql, $params);

        // edit
        } else {
            if ($sortOrder < $model->getOrigData('sort_order')) {
                $sql = "update faq_entity set sort_order = sort_order + 1 where category_id = ? and sort_order >= ? and sort_order < ? ";
                $params = array($model->getCategoryId(), $sortOrder, $model->getOrigData('sort_order'));
                $conn->query($sql, $params);
            } elseif ($sortOrder > $model->getOrigData('sort_order')) {
                $sql = "update faq_entity set sort_order = sort_order - 1 where category_id = ? and sort_order > ? and sort_order <= ? ";
                $params = array($model->getCategoryId(), $model->getOrigData('sort_order'), $sortOrder);
                $conn->query($sql, $params);
            }
        }
    }
}
