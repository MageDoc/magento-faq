<?php

class IssohSystems_Faq_Model_Resource_Feedback extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct()
    {
        $this->_init('faq/feedback','feedback_id');
    }
}