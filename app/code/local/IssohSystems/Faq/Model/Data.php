<?php
class IssohSystems_Faq_Model_Data extends IssohSystems_Faq_Model_Abstract
{

    function _construct() {
        $this->_init('faq/data');
    }

    /*
    * If object is new add updated date
    * 
    * @return IssohSystems_Faq_Model_Data
    */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if ($this->isObjectNew()) {
            $this->setData('created_at', Varien_Date::now());
        }

        $this->setData('updated_at', Varien_Date::now());

        return $this;
    }

    public function getDataByStore($faq_id, $store_id) {
        $collection = $this->getCollection()
                           ->addFieldToFilter('faq_id', $faq_id)
                           ->addFieldToFilter('store_id', $store_id)
                           ->getFirstItem();

        return $collection;
    }
}
