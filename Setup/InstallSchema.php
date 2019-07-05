<?php

namespace Radarsofthouse\Reepay\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 *
 * @package Radarsofthouse\Reepay\Setup
 */
class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $table_radarsofthouse_reepay_status = $setup->getConnection()->newTable($setup->getTable('radarsofthouse_reepay_status'));

        $table_radarsofthouse_reepay_status->addColumn(
            'status_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
            'Entity ID'
        );

        $table_radarsofthouse_reepay_status->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'order_id'
        );

        $table_radarsofthouse_reepay_status->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'status'
        );

        $table_radarsofthouse_reepay_status->addColumn(
            'first_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'first_name'
        );

        $table_radarsofthouse_reepay_status->addColumn(
            'last_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'last_name'
        );

        $table_radarsofthouse_reepay_status->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'email'
        );

        $table_radarsofthouse_reepay_status->addColumn(
            'token',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'token'
        );

        $table_radarsofthouse_reepay_status->addColumn(
            'masked_card_number',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'masked_card_number'
        );

        $table_radarsofthouse_reepay_status->addColumn(
            'fingerprint',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'fingerprint'
        );

        $table_radarsofthouse_reepay_status->addColumn(
            'card_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'card_type'
        );

        $table_radarsofthouse_reepay_status->addColumn(
            'error',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'error'
        );

        $table_radarsofthouse_reepay_status->addColumn(
            'error_state',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'error_state'
        );

        $setup->getConnection()->createTable($table_radarsofthouse_reepay_status);
    }
}
