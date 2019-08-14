<?php
class XT_Brand_Block_Brand extends Mage_Core_Block_Template
{	
     public function getBrandItems()     
     { 
       $_storeId =  Mage::app()->getStore()->getId();
	   $_brandId = $this->getRequest()->getParam('id');
	   $collection = Mage::getModel('brand/grid')->getCollection();
	   $collection->addFieldToFilter('brand_id',$_brandId);
	   $collection->addFieldToFilter('store_id',$_storeId);
	   return $collection;
     }
	 
	 public function getBrandDetails()     
     { 
	   $_brandId = $this->getRequest()->getParam('id');
	   $collection = Mage::getModel('brand/brand')->getCollection();
	   $collection->addFieldToFilter('brand_id',$_brandId);
	   return $collection->getData();
     }
	 
	  public function __construct()
	  {
		parent::__construct();
	  }
	
	  public function _prepareLayout()
	  {
		  
	  }
	  
	  public function getProductCollection(){		  
		  $_storeId =  Mage::app()->getStore()->getId();
		  $_brandId = $this->getRequest()->getParam('id');
		  $productCollection = Mage::getModel('brand/grid')->getCollection();
		  $productCollection->addFieldToFilter('brand_id',$_brandId);
		  $productCollection->addFieldToFilter('store_id',$_storeId);
		  $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
		  $pager->setAvailableLimit(array(8=>8,12=>12,16=>16,20=>20,'all'=>'All'));
		  $pager->setCollection($productCollection);
		  $this->setChild('pager', $pager);
	
		  return $productCollection;
	  }
	  
	  public function getPagerHtml(){
		  return $this->getChildHtml('pager');
	  }		
	  
	  public function getPriceHtml($product)
	  {
			$this->setTemplate('catalog/product/price.phtml');
			$this->setProduct($product);
			return $this->toHtml();
	  }
	  
	  public function getAddToCartUrl($product, $additional = array())
      {
        if ($product->getTypeInstance(true)->hasRequiredOptions($product)) {
            if (!isset($additional['_escape'])) {
                $additional['_escape'] = true;
            }
            if (!isset($additional['_query'])) {
                $additional['_query'] = array();
            }
            $additional['_query']['options'] = 'cart';

            return $this->getProductUrl($product, $additional);
        }
        return $this->helper('checkout/cart')->getAddUrl($product, $additional);
      }	  
	  
}