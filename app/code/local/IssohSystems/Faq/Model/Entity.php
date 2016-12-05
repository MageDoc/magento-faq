<?php
class IssohSystems_Faq_Model_Entity extends IssohSystems_Faq_Model_Abstract
{
    const ACTIVE_TRUE  = 1;
    const ACTIVE_FALSE = 0;

    function _construct() {
        $this->_init('faq/entity');
    }

}
