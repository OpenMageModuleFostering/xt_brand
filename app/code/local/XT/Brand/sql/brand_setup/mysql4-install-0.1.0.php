<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('customer_brand')};
CREATE TABLE {$this->getTable('customer_brand')} (
  `brand_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `description` text NOT NULL default '',
  `brand_url` varchar(255) NOT NULL default '',
  PRIMARY KEY (`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('grid_brand')};
CREATE TABLE {$this->getTable('grid_brand')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `brand_id` int(11) NOT NULL ,
  `product_id` int(11) NOT NULL ,
  `position` int(11) NOT NULL default 0,
  `store_id` smallint(6) NOT NULL default '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

/*$installer->run("
INSERT INTO `{$installer->getTable('customer_brand')}`
(`brand_id`, `title`) values (1,'Generic Brand Products');
");*/

$installer->endSetup(); 