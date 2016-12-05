<?php
$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();

// faq_entity
$faq_entity = $connection
    ->newTable($installer->getTable('faq/entity'))
    ->addColumn('faq_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'faq Id')
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'unsigned'  => true,
        'nullable'  => false
        ), 'category Id')
    ->addColumn('active', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
        'nullable'  => false,
        ), 'active')
    ->setComment('Faq entity');

$connection->createTable($faq_entity);

// faq_data
$faq_data = $connection
    ->newTable($installer->getTable('faq/data'))
    ->addColumn('faq_data_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'faq data Id')
    ->addColumn('faq_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'unsigned'  => true,
        'nullable'  => false
        ), 'faq id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'unsigned'  => true,
        'nullable'  => false
        ), 'Store Id')
    ->addColumn('question', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        'nullable'  => false
        ), 'Question')
    ->addColumn('answer', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        'nullable'  => false
        ), 'Answer')
    ->addColumn('related_faq', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true
        ), 'related faq')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => true
        ), 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => true
        ), 'Updated At')
    ->setComment('Faq data');

$connection->createTable($faq_data);

// faq_category
$faq_category = $connection
    ->newTable($installer->getTable('faq/category'))
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'category Id')
    ->addColumn('active', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
        'nullable'  => false,
        ), 'active')
    ->addColumn('level', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'unsigned'  => true,
        'nullable'  => false
        ), 'Level')
    ->addColumn('parent', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'unsigned'  => true,
        'nullable'  => false
        ), 'Parent Id')
    ->addColumn('icon_class', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true
        ), 'Icon Class')
    ->addColumn('icon_color', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true
        ), 'Icon Class')
    ->setComment('Faq category');

$connection->createTable($faq_category);

// faq_category_data
$faq_category_data = $connection
    ->newTable($installer->getTable('faq/category_data'))
    ->addColumn('category_data_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Category Data Id')
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'unsigned'  => true,
        'nullable'  => false
        ), 'Category Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'unsigned'  => true,
        'nullable'  => false
        ), 'Store Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'Name')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => true
        ), 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => true
        ), 'Updated At')
    ->setComment('Category Data');

$connection->createTable($faq_category_data);

// faq_feedback
$faq_feedback = $connection
    ->newTable($installer->getTable('faq/feedback'))
    ->addColumn('feedback_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'feedback Id')
    ->addColumn('faq_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'unsigned'  => true,
        'nullable'  => false
        ), 'Faq Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'unsigned'  => true,
        'nullable'  => true
        ), 'Customer Id')
    ->addColumn('content', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
        'unsigned'  => true,
        'nullable'  => false
        ), 'Content')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => true
        ), 'Created At')
    ->setComment('Faq feedback');

$connection->createTable($faq_feedback);

$installer->endSetup();