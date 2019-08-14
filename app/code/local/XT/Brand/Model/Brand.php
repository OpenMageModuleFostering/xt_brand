<?php

class XT_Brand_Model_Brand extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('brand/brand');
    }
	public function getproids()
	{
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$result = $connection->query("select * from grid_brand order by position");
		while ($row = $result->fetch() ) {
		$ids[]=$row['product_id'];
		}
		return $ids;
	}
}