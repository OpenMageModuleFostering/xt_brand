<?php 
class XT_Brand_Block_Adminhtml_Brand_Edit_Tab_Store extends Mage_Adminhtml_Block_Widget_Grid 
{
	public function __construct()
	{
        parent::__construct();
        $this->setTemplate('brand/stores.phtml');
	}
		
}