<?php

class XT_Brand_Block_Adminhtml_Brand_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('brand_form', array('legend'=>Mage::helper('brand')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('brand')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('brand')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));	
	  
	  $fieldset->addField('brand_url', 'text', array(
          'label'     => Mage::helper('brand')->__('Brand Url'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'brand_url',
      ));
     
      $fieldset->addField('description', 'editor', array(
          'name'      => 'description',
          'label'     => Mage::helper('brand')->__('Description'),
          'title'     => Mage::helper('brand')->__('Description'),
          'style'     => 'width:450px; height:200px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getBrandData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getBrandData());
          Mage::getSingleton('adminhtml/session')->setBrandData(null);
      } elseif ( Mage::registry('brand_data') ) {
          $form->setValues(Mage::registry('brand_data')->getData());
      }
      return parent::_prepareForm();
  }
}