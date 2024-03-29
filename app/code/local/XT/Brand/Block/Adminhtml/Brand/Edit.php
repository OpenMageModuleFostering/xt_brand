<?php     

class XT_Brand_Block_Adminhtml_Brand_Edit extends Mage_Adminhtml_Block_Widget_Form_Container  
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'brand';
        $this->_controller = 'adminhtml_brand';
        
        $this->_updateButton('save', 'label', Mage::helper('brand')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('brand')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('brand_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'brand_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'brand_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
		$this->_removeButton('save');
		$this->_removeButton('delete');
    }

    public function getHeaderText()
    {
        if( Mage::registry('brand_data') && Mage::registry('brand_data')->getId() ) {
            return Mage::helper('brand')->__('Manage Brand Products');
        } else {
            return Mage::helper('brand')->__('Manage Brand Products');
        }
    }
}