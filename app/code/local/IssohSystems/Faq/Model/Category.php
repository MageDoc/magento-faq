<?php
class IssohSystems_Faq_Model_Category extends IssohSystems_Faq_Model_Abstract
{
    const ACTIVE_TRUE  = 1;
    const ACTIVE_FALSE = 0;

    const LEVEL_PARENT  = 0;
    const LEVEL_CHILD   = 1;

    const PARENT_CATEGORY_ID  = 0;

    function _construct() {
        $this->_init('faq/category');
    }

}
