<?php

class IssohSystems_Adminhtml_Block_Render_Faq_Category_Name extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $iconClass = $row->getIconClass();
        $iconColor = $row->getIconColor();
        $name = $row->getName();

        $value = sprintf('<i class="fa fa-2x %s" style="color:#%s"></i>%s', $iconClass, $iconColor, $name);
        return $value;
    }
}