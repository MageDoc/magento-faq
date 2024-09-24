<?php
$installer = $this;
$installer->startSetup();

$connection = $installer->getConnection();

$tableName = 'faq/entity';

$connection->addIndex(
    $this->getTable($tableName),
    $connection->getIndexName(
        $this->getTable($tableName),
        array('category_id')
    ),
    array('category_id')
);

$connection->addIndex(
    $this->getTable($tableName),
    $connection->getIndexName(
        $this->getTable($tableName),
        array('active')
    ),
    array('active')
);

$tableName = 'faq/data';

$connection->addIndex(
    $this->getTable($tableName),
    $connection->getIndexName(
        $this->getTable($tableName),
        array('faq_id', 'store_id')
    ),
    array('faq_id', 'store_id')
);

$tableName = 'faq/category';

$connection->addIndex(
    $this->getTable($tableName),
    $connection->getIndexName(
        $this->getTable($tableName),
        array('parent')
    ),
    array('parent')
);

$tableName = 'faq/category_data';

$connection->addIndex(
    $this->getTable($tableName),
    $connection->getIndexName(
        $this->getTable($tableName),
        array('category_id', 'store_id')
    ),
    array('category_id', 'store_id')
);

$installer->endSetup();
