<?php

class XT_Brand_Model_Mysql4_Brand_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('brand/brand');
    }
}