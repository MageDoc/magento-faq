<?php

class IssohSystems_Adminhtml_Block_Faq_Items_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct() {
        parent::__construct();

        $this->_objectId = 'faq_items_edit_id';
        $this->_blockGroup = 'issohadmin';
        $this->setId('faqItemsEdit');
        $this->_controller = 'faq_items';

        $model = Mage::registry('current_faq');
        if (!($model && $model->getId())) {
            $this->_formScripts[] = "
                \$j(function(){
                    parent_changed(\$j('#category_id').val());
                    \$j('#category_id').change(function(){
                        parent_changed(\$j(this).val());
                    });
                });

                function parent_changed(value) {
                    var data = {'form_key': \$j('input[name=form_key]').val(), 'category_id': value};
                    var url = '{$this->getUrl('*/*/sortorder')}';
                    url = url + (url.match(new RegExp('\\\\?')) ? '&isAjax=true' : '?isAjax=true');
                    \$j.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: url,
                        data: data,
                        success: function(response){
                            if (response['sort_order'] !== 'undefined') {
                                \$j('#sort_order').val(response['sort_order']);
                            }
                        }
                     });
                }
            ";
        }

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('answer') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'answer');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'answer');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
            ";
    }

    protected function _prepareLayout() {

        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }

    }

    public function getHeaderText() {
        if( Mage::registry('faq_data') && Mage::registry('faq_data')->getId() ) {
            return Mage::helper('faq')->__("Edit FAQ '%s'", $this->htmlEscape(Mage::registry('faq_data')->getQuestion()));
        } else {
            return Mage::helper('faq')->__('Add FAQ');
        }
    } 
}