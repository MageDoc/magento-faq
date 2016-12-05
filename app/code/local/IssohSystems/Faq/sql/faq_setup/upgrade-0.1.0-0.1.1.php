<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()
->addColumn($installer->getTable('faq/entity'),'sort_order', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
    'nullable'  => true,
    'default'   => null,
    'comment'   => 'Sort Order'
    ));

$installer->getConnection()
->addColumn($installer->getTable('faq/category'),'sort_order', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
    'nullable'  => true,
    'default'   => null,
    'comment'   => 'Sort Order'
    ));

$categories = Mage::getModel('faq/category')->getCollection();
$splitArray = array();
foreach($categories as $category) {
    $splitArray[$category->getParent()][] = $category;
}
foreach($splitArray as $parent => $items) {
    $sortOrder = 1;
    foreach($items as $item) {
        $item->setSortOrder($sortOrder++);
        $item->save();
    }
}

$faqs = Mage::getModel('faq/entity')->getCollection();
$splitArray = array();
foreach($faqs as $faq) {
    $splitArray[$faq->getCategoryId()][] = $faq;
}
foreach($splitArray as $parent => $items) {
    $sortOrder = 1;
    foreach($items as $item) {
        $item->setSortOrder($sortOrder++);
        $item->save();
    }
}

$installer->endSetup();
