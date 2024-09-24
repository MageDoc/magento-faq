<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   BL
 * @package    BL_CustomGrid
 * @copyright  Copyright (c) 2012 Benoît Leulliette <benoit.leulliette@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class IssohSystems_Adminhtml_Model_Grid_Type_Faq
    extends BL_CustomGrid_Model_Grid_Type_Abstract
{
    public function isAppliableToGrid($type, $rewritingClassName)
    {
        return ($type == 'issohadmin/faq_items_grid');
    }
    
    public function checkUserEditPermissions($type, $model, $block=null, $params=array())
    {
        if (parent::checkUserEditPermissions($type, $model, $block, $params)) {
            return Mage::getSingleton('admin/session')->isAllowed('admin/faq/items');
        }
        return false;
    }

    protected function _getEntityRowIdentifiersKeys($type)
    {
        return array('faq_id', 'store_id');
    }

    protected function _loadEditedEntity($type, $config, $params)
    {
        if (isset($params['ids']['faq_id'])) {
            $id = $params['ids']['faq_id'];

            return in_array($config['id'], array('active', 'sort_order'))
                ? Mage::getModel('faq/entity')->load($id)
                : Mage::getModel('faq/data')->getDataByStore($id, $params['ids']['store_id']) ;
        }
        return null;
    }

    protected function _getLoadedEntityName($type, $config, $params, $entity)
    {
        return $entity->getQuestion();
    }

    protected function _getBaseEditableFields($type)
    {
        $helper = Mage::helper('faq');

        $fields = array(
            'question' => array(
                'type'     => 'textarea',
                'required' => true,
                'form_style' => 'width:100%;height:5em;'
            ),
            'answer' => array(
                'type'     => 'textarea',
                'required' => true,
                'form_style' => 'width:100%;height:5em;'
            ),
            'active' => array(
                'type'         => 'select',
                'required'     => true,
                'form_options' => array(
                    '1' => $helper->__('Yes'),
                    '0' => $helper->__('No'),
                ),
            ),
            'sort_order' => array(
                'type'     => 'text',
                'required' => true,
                'form_class'   => 'validate-not-negative-number',
            ),
        );

        return $fields;
    }
}
