<?php 
class XT_Brand_Block_Adminhtml_Brand_Edit_Tab_Grid extends Mage_Adminhtml_Block_Widget_Grid 
{
	public function __construct() 
	{
		parent::__construct();
		$this->setId('upsellGrid');
		$this->setUseAjax(true); // Using ajax grid is important
		$this->setDefaultSort('entity_id');
		$this->setDefaultFilter(array('in_products'=>1)); // By default we have added a filter for the rows, that in_products value to be 1
		$this->setDefaultDir('ASC');
	    $this->setDefaultLimit(200);
		$this->setSaveParametersInSession(false);  //Dont save paramters in session or else it creates problems
	}

	protected function _prepareCollection()
	{
		$storeId = Mage::getSingleton('adminhtml/session')->getData('brand_store_id');
		if(empty($storeId))
		{
			$storeId = "1";
		}
		$collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('price')
			->addAttributeToSelect('status')
            ->addAttributeToSelect('visibility')
            //->addStoreFilter($storeId)
            ->joinField('position',
                'brand/grid',
                'position',
                'product_id=entity_id',
                '{{table}}.store_id ='.$storeId,
                'left')
		    //->addAttributeToFilter('type_id', array('eq' => 'simple'))
			->addFieldToFilter('status',Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
			->addAttributeToFilter('visibility', array('neq' => 1));
        $this->setCollection($collection);

       // return parent::_prepareCollection();
		
		parent::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();
        return $this;
	}

	protected function _addColumnFilterToCollection($column)
	{
		// Set custom filter for in product flag
		if ($column->getId() == 'in_products') {
			$ids = $this->_getSelectedCustomers();
			if (empty($ids)) {
				$ids = 0;
			}
			if ($column->getFilter()->getValue()) {
				$this->getCollection()->addFieldToFilter('entity_id', array('in'=>$ids));
			} else {
				if($productIds) {
					$this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$ids));
				}
			}
		}
		else if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField('websites',
                    'catalog/product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left');
            }
           return parent::_addColumnFilterToCollection($column);
		}
		else {
			parent::_addColumnFilterToCollection($column);
		}
		return $this;
	}

	protected function _prepareColumns()
	{

		    $this->addColumn('in_products', array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'customer',
                'values'            => $this->_getSelectedCustomers(),
                'align'             => 'center',
                'index'             => 'entity_id'
            ));
			
            $this->addColumn('id', array(
            'header'    => Mage::helper('brand')->__('Product Id'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'entity_id'
        ));
			
			$this->addColumn('name', array(
				'header'    => Mage::helper('brand')->__('Name'),
				'index'     => 'name'
			));
			$this->addColumn('sku', array(
				'header'    => Mage::helper('brand')->__('SKU'),
				'width'     => '120px',
				'index'     => 'sku'
			));
			
			$this->addColumn('type', array(
				'header'    => Mage::helper('brand')->__('Type'),
				'width'     => 100,
				'index'     => 'type_id',
				'type'      => 'options',
				'options'   => Mage::getSingleton('catalog/product_type')->getOptionArray(),
			));
	
			$sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
				->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
				->load()
				->toOptionHash();
	
			$this->addColumn('set_name', array(
				'header'    => Mage::helper('brand')->__('Attrib. Set Name'),
				'width'     => 130,
				'index'     => 'attribute_set_id',
				'type'      => 'options',
				'options'   => $sets,
			));
	
			$this->addColumn('status', array(
				'header'    => Mage::helper('brand')->__('Status'),
				'width'     => 90,
				'index'     => 'status',
				'type'      => 'options',
				'options'   => Mage::getSingleton('catalog/product_status')->getOptionArray(),
			));
	
			$this->addColumn('visibility', array(
				'header'    => Mage::helper('brand')->__('Visibility'),
				'width'     => 90,
				'index'     => 'visibility',
				'type'      => 'options',
				'options'   => Mage::getSingleton('catalog/product_visibility')->getOptionArray(),
			));
	
			$this->addColumn('price', array(
				'header'    => Mage::helper('brand')->__('Price'),
				'type'  => 'currency',
				'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
				'index'     => 'price'
			));
			
			 if (!Mage::app()->isSingleStoreMode()) {
				$this->addColumn('websites',
					array(
						'header'=> Mage::helper('brand')->__('Websites'),
						'width' => '100px',
						'sortable'  => false,
						'index'     => 'websites',
						'type'      => 'options',
						'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
				));
			}
			
            $this->addColumn('position', array(
            'header'            => Mage::helper('brand')->__('Position'),
            'name'              => 'position',
            'width'             =>  60,
            'type'              => 'number',
            'validate_class'    => 'validate-number',
            'index'             => 'position',
            'editable'          => true,
            'edit_only'         => true
            ));

            return parent::_prepareColumns();
	}

	protected function _getSelectedCustomers()   // Used in grid to return selected customers values.
	{
		$customers = array_keys($this->getSelectedCustomers());
		return $customers;
	}

	public function getGridUrl()
	{
		return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/upsellgrid', array('_current'=>true));
	}
	public function getSelectedCustomers()
	{
		// Customer Data
		$storeId = Mage::getSingleton('adminhtml/session')->getData('brand_store_id');
		$tm_id = $this->getRequest()->getParam('id');
		if(!isset($tm_id)) {
			$tm_id = 0;
		}
		$collection = Mage::getModel('brand/grid')->getCollection();
		$collection->addFieldToFilter('store_id',$storeId);
		$collection->addFieldToFilter('brand_id',$tm_id);
		$custIds = array();
		foreach($collection as $obj){
			$custIds[$obj->getProductId()] = array('position'=>$obj->getPosition());
		}
		return $custIds;
	}


}