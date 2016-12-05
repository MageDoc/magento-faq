<?php
class IssohSystems_Faq_Block_Category extends Mage_Core_Block_Template
{
    public function __construct(){
        parent::__construct();

        $store = Mage::app()->getStore()->getId();
        $collection = Mage::getModel('faq/category')->getCollection()->addFieldToFilter('active', IssohSystems_Faq_Model_Entity::ACTIVE_TRUE);
        $collection->getSelect()
                    ->join(array('category' => 'faq_category_data'), 'category.category_id = main_table.category_id AND category.store_id='. $store, array('name'));

        $collection->setOrder('parent', 'ASC');
        $collection->setOrder('sort_order', 'ASC');

        $this->setCollection($this->_splitCategories($collection));
    }

    private function _splitCategories($collection) {
        $splitCategories = array();
        foreach ($collection as $category) {
            $splitCategories[$category->getParent()][] = $category;
        }

        return $splitCategories;
    }

    protected function _prepareLayout(){
        parent::_prepareLayout();
    }

    public function getCurrentCategory() {
        return Mage::registry('current_category');
    }
}
