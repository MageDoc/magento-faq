<?php

class IssohSystems_Adminhtml_Block_Render_Faq_Items_Parent_Name extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $caretoryId = $row->getParent();
        $storeId = $row->getStoreId();

        $model = Mage::getModel('faq/category_data')->getCollection()
                                                    ->addFieldToFilter('category_id', $caretoryId)
                                                    ->addFieldToFilter('store_id', $storeId)
                                                    ->getFirstItem();
        $name = $model->getName();

        return $name;
    }
}