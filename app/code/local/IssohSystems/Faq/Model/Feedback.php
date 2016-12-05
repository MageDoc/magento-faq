<?php
class IssohSystems_Faq_Model_Feedback extends IssohSystems_Faq_Model_Abstract
{
    const CONTENT_OK  = 1;
    const CONTENT_NG = 0;

    function _construct() {
        $this->_init('faq/feedback');
    }

    /**
     * @param $faq_id
     * @param $customer_id
     * @param $content
     *
     * @return IssohSystems_Faq_Model_Feedback
     */
    public function getItemByContent($faq_id, $customer_id, $content) {
        if (is_null($this->_item)) {
            $collection = $this->getCollection()
                               ->addFieldToFilter('faq_id', $faq_id)
                               ->addFieldToFilter('customer_id', $customer_id)
                               ->addFieldToFilter('content', $content)
                               ->getFirstItem();

            $this->_item = $collection;
        }

        return $this->_item;
    }
}
