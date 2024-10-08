<?php

class IssohSystems_Adminhtml_Block_Faq_Items_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm() {
        $form = Mage::helper('faq')->isModuleEnabled('MageDoc_System')
            ? new MageDoc_System_Block_Adminhtml_Form_Extended()
            : new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('faq_form', array('legend' => Mage::helper('faq')->__('FAQ')));

        $store = Mage::registry('store_id');
        $model = Mage::registry('current_faq');

        $faqData = array();
        if (Mage::getSingleton('adminhtml/session')->getFaqData()) {
            $faqData = Mage::getSingleton('adminhtml/session')->getFaqData();
            Mage::getSingleton('adminhtml/session')->setFaqData(null);
        } elseif (Mage::registry('current_faq')) {
            if (is_null(Mage::registry('current_faq')->getActive())) {
                Mage::registry('current_faq')->setData('active', IssohSystems_Faq_Model_Entity::ACTIVE_TRUE);
            }
            $faqData = Mage::registry('current_faq')->getData();
        }

        if (Mage::helper('faq')->isModuleEnabled('Testimonial_MageDoc')) {
            $form->addType('combobox', 'MageDoc_System_Block_Adminhtml_Form_Element_Combobox');
            $categoryId = isset($faqData['category_id'])
                ? $faqData['category_id']
                : null;
            $fieldset->addField('category_id', 'combobox', array(
                'label'     => Mage::helper('faq')->__('Category'),
                'title'     => Mage::helper('faq')->__('Category'),
                'name'      => 'category_id',
                'value'     => $categoryId,
                'options'   => array(),
                'class'     => 'required-entry',
                'source_url'=> $this->getUrl('magedoc/adminhtml_category/list'),
                'settings'  => array(
                    'isAjax'   => true
                )
            ));
        } else {
            $fieldset->addField('category_id', 'select', array(
                'name'      => 'category_id',
                'label'     => Mage::helper('faq')->__('Category'),
                'required'  => true,
                'values'    => $this->toCategoryOptionArray(),
            ));
        }

        $fieldset->addField('active', 'radios', array(
            'label'    => Mage::helper('faq')->__('Active'),
            'name'     => 'active',
            'class'    => 'validate-one-required-by-name',
            'values'   => $this->toActiveOptionArray(),
        ));

        $fieldset->addField('sort_order', 'text', array(
            'label'    => Mage::helper('faq')->__('Sort Order'),
            'name'     => 'sort_order',
            'required' => true,
        ));

        $fieldset->addField('question', 'text', array(
            'label'    => Mage::helper('faq')->__('Question'),
            'class'    => 'required-entry',
            'required' => true,
            'name'     => 'question',
        ));

        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config');
        $fieldset->addField('answer', 'editor', array(
            'name'     => 'answer',
            'label'    => Mage::helper('faq')->__('Answer'),
            'title'    => Mage::helper('faq')->__('Answer'),
            'style'    => 'width:700px; height:500px;',
            'config'   => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            'wysiwyg'  => true,
            'required' => true,
        ));

        $fieldset->addField('store_id', 'hidden', array(
            'label'    => Mage::helper('faq')->__('Store Id'),
            'required' => false,
            'name'     => 'store_id'
        ));

        Mage::dispatchEvent('faq_item_edit_prepare_form', array('form' => $form));

        $form->setValues($faqData);
    }

    protected function toCategoryOptionArray() {
        $store = Mage::registry('store_id');
        $collection = Mage::getModel('faq/category')->getCollection();

        $collection->getSelect()
                   ->joinLeft(array('category' => 'faq_category_data'), 'category.category_id = main_table.category_id AND category.store_id='. $store, array('name'));

        $resultArray = array();
        $resultArraySplit = array();
        $resultArrayParent = array();
        foreach($collection as $category) {
            if ( intval($category->getParent()) === IssohSystems_Faq_Model_Category::PARENT_CATEGORY_ID ) {
                $resultArrayParent[$category->getCategoryId()] = $category->getName();
            } else {
                $resultArraySplit[$category->getParent()][] = array('value' => $category->getCategoryId(), 'label' => $category->getName());
            }
        }

        ksort($resultArraySplit);
        foreach ($resultArraySplit as $parent => $options) {
            $resultArray[] = array('value' => $resultArraySplit[$parent], 'label' => $resultArrayParent[$parent]);
        }

        return $resultArray;
    }

    protected function toActiveOptionArray()
    {
        $options = Mage::helper('faq')->getActiveOptionArray();
        $result = array();
        foreach($options as $key => $value) {
            $result[] = array('value' => $key, 'label' => $value);
        }
        return $result;
    }
}
