<?php

class IssohSystems_Adminhtml_Faq_CategoryController extends Mage_Adminhtml_Controller_Action
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
        $this->_setActiveMenu('faq/category');

        /**
         * Append customers block to content
         */
        $this->_addContent(
            $this->getLayout()->createBlock('issohadmin/faq_category')
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

        if ($id) {
            $collection = Mage::getModel('faq/category')->getCollection()->addFieldToFilter('main_table.category_id', $id);
            $collection->getSelect()->joinLeft(array('data'=> 'faq_category_data'), 'data.category_id = main_table.category_id AND data.store_id='. $store, array('name', 'store_id'));
            $model = $collection->getFirstItem();
            if ($model && $model->getId()){
                $model =  $model->setActive($model->getActive())
                                ->setName($model->getName())
                                ->setParent($model->getParent());
            }
        }

        if (($model && $model->getId()) || Mage::registry('faq_new_action') || is_null($id)){
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('store_id', $store);
            Mage::register('current_category', $model);

            $this->loadLayout();

            $this->_addContent($this->getLayout()->createBlock('issohadmin/faq_category_edit'))
                 ->_addLeft($this->getLayout()->createBlock('adminhtml/store_switcher'))
                 ->_addLeft($this->getLayout()->createBlock('issohadmin/faq_category_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('faq')->__('Category does not exist'));
            $this->_redirect('*/*/', array('store' => $store));
        }
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {

            $model = Mage::getModel('faq/category');
            $model_text = Mage::getModel('faq/category_data');

            try {
                $editFlag = false;
                $defaultStoreId = intval(Mage::helper('faq')->getDefaultStoreId());
                $category_id = $this->getRequest()->getParam('id');
                if ($category_id) {
                    $model = $model->load($category_id);
                    if (!$model->getId()) {
                        $this->_redirect('*/*/');
                    }
                    $editFlag = true;
                }

                $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
                $connection->beginTransaction();

                $model->setActive($data['active']);
                $model->setIconColor($data['icon_color']);
                $model->setIconClass($data['icon_class']);
                $model->setParent($data['parent']);
                $model->setSortOrder($data['sort_order']);

                $level = IssohSystems_Faq_Model_Category::LEVEL_CHILD;
                if (intval($data['parent']) === IssohSystems_Faq_Model_Category::PARENT_CATEGORY_ID) {
                    $level = IssohSystems_Faq_Model_Category::LEVEL_PARENT;
                }
                $model->setLevel($level);

                $this->updateSortOrder($connection, $model, $data['sort_order']);

                $model->save();

                $category_id = $model->getId();
                $data['category_id'] = $category_id;

                // edit
                if ($editFlag) {
                    if ($data['store_id'] == 0) {
                        $text_data = $this->_initSaveCategory(0, $data);
                        $text_data->save();

                        foreach (Mage::app()->getWebsites() as $website) {
                            foreach ($website->getGroups() as $group) {
                                $stores = $group->getStores();
                                foreach ($stores as $store) {
                                    if ($defaultStoreId === intval($store->getId())) {
                                        $text_data = $this->_initSaveCategory($store->getId(), $data);
                                        $text_data->save();
                                    }
                                }
                            }
                        }

                    } else {
                        $text_data = $this->_initSaveCategory($data['store_id'], $data);
                        $text_data->save();
                    }
                // new
                } else {

                    $text_data = $this->_initSaveCategory(0, $data);
                    $text_data->save();

                    foreach (Mage::app()->getWebsites() as $website) {
                        foreach ($website->getGroups() as $group) {
                            $stores = $group->getStores();
                            foreach ($stores as $store) {
                                $text_data = $this->_initSaveCategory($store->getId(), $data);
                                $text_data->save();
                            }
                        }
                    }
                }

                $connection->commit();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('faq')->__('Category was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $category_id, 'store' => $data['store_id']));
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
                $faqCollection = Mage::getModel('faq/entity')->getCollection()->addFieldToFilter('category_id', $id);
                if ($faqCollection->getSize() > 0) {
                    Mage::getSingleton('adminhtml/session')->addError('Please remove FAQ first from this category.');
                    $this->_redirect('*/*/edit', array('id' => $id));
                    return;
                }

                $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
                $connection->beginTransaction();

                $model = Mage::getModel('faq/category')->load($id);
                $sql = "update faq_category_entity set sort_order = sort_order - 1 where parent = ? and sort_order > ? ";
                $params = array($model->getParent(), $model->getSortOrder());
                $connection->query($sql, $params);

                $where = $connection->quoteInto('category_id=?', $id);
                $table = Mage::getModel('faq/category')->getResource()->getMainTable();
                $connection->delete($table, $where);

                $table = Mage::getModel('faq/category_data')->getResource()->getMainTable();
                $connection->delete($table, $where);

                $connection->commit();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Category was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                $connection->rollback();
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $id));
            }
        }
        $this->_redirect('*/*/');
    }

    private function _initSaveCategory($store_id, $data) {
        $text_data = Mage::getModel('faq/category_data')->getDataByStore($data['category_id'], $store_id);
        $text_data->setStoreId($store_id);
        $text_data->setCategoryId($data['category_id']);
        $text_data->setName($data['name']);
        return $text_data;
    }

    public function sortorderAction() {
        $response = array();
        $response['sort_order'] = 1;
        if (!is_null($pid = $this->getRequest()->getPost('parent'))) {
            $collection =  Mage::getModel('faq/category')->getCollection()->addFieldToFilter('parent', $pid);
            $response['sort_order'] = $collection->getSize();
            $response['sort_order'] += 1;
        }

        $this->getResponse()->setBody(json_encode($response));
    }

    private function updateSortOrder($conn, $model, $sortOrder) {

        // new
        if (!$model->getId()) {
            $sql = "update faq_category_entity set sort_order = sort_order + 1 where parent = ? and sort_order >= ? ";
            $params = array($model->getParent(), $sortOrder);
            $conn->query($sql, $params);

        // edit
        } else {
            if ($sortOrder < $model->getOrigData('sort_order')) {
                $sql = "update faq_category_entity set sort_order = sort_order + 1 where parent = ? and sort_order >= ? and sort_order < ? ";
                $params = array($model->getParent(), $sortOrder, $model->getOrigData('sort_order'));
                $conn->query($sql, $params);
            } elseif ($sortOrder > $model->getOrigData('sort_order')) {
                $sql = "update faq_category_entity set sort_order = sort_order - 1 where parent = ? and sort_order > ? and sort_order <= ? ";
                $params = array($model->getParent(), $model->getOrigData('sort_order'), $sortOrder);
                $conn->query($sql, $params);
            }
        }
    }
}
