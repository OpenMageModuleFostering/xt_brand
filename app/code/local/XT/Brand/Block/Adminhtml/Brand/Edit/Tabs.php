<?php 

class XT_Brand_Block_Adminhtml_Brand_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs 
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('brand_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('brand')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
	  $this->addTab('store_section', array(
          'label'     => Mage::helper('brand')->__('Select Stores'),
          'title'     => Mage::helper('brand')->__('Select Stores'),
          'content'   => $this->getLayout()->createBlock('brand/adminhtml_brand_edit_tab_store')->toHtml(),
      ));
	  
	  $this->addTab('form_section', array(
          'label'     => Mage::helper('brand')->__('Brand Information'),
          'title'     => Mage::helper('brand')->__('Brand Information'),
          'content'   => $this->getLayout()->createBlock('brand/adminhtml_brand_edit_tab_form')->toHtml(),
      ));
	   
      $this->addTab('grid_section', array(
          'label'     => Mage::helper('brand')->__('Brand Products'),
          'title'     => Mage::helper('brand')->__('Brand Products'),
          'url'       => $this->getUrl('*/*/grid', array('_current' => true)),
          'class'     => 'ajax',
      ));
      
      return parent::_beforeToHtml();
  }
}