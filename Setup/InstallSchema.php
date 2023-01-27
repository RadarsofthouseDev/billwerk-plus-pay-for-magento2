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
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productMetadata = $objectManager->get(\Magento\Framework\App\ProductMetadataInterface::class);
        $magentoMinorVersion = (int)explode(".", $productMetadata->getVersion())[1];
        if($magentoMinorVersion >= 3 ){
            return;
        }


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

        $setup->startSetup();

        $quoteTable = 'quote';
        $orderTable = 'sales_order';
        $invoiceTable = 'sales_invoice';
        $creditmemoTable = 'sales_creditmemo';

        //Setup two columns for quote and order

        //Quote tables
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteTable),
                'reepay_surcharge_fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Surcharge Fee'

                ]
            );

        $setup->getConnection()
            ->addColumn(
                $setup->getTable($quoteTable),
                'reepay_credit_card',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '255',
                    'default' => null,
                    'nullable' => true,
                    'comment' => 'Reepay credit card'
                ]
            );

        //Order tables
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderTable),
                'reepay_surcharge_fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Surcharge Fee'

                ]
            );
        
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderTable),
                'reepay_credit_card',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'default' => null,
                    'nullable' => true,
                    'comment' => 'Reepay credit card'
                ]
            );

        //Invoice tables
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($invoiceTable),
                'reepay_surcharge_fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Surcharge Fee'

                ]
            );
        //Credit memo tables
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($creditmemoTable),
                'reepay_surcharge_fee',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' => 'Surcharge Fee'

                ]
            );
        $setup->endSetup();

        $table_radarsofthouse_reepay_customer = $setup->getConnection()->newTable($setup->getTable('radarsofthouse_reepay_customer'));

        $table_radarsofthouse_reepay_customer->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'Entity ID'
        );

        $table_radarsofthouse_reepay_customer->addColumn(
            'magento_customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'customer id'
        );

        $table_radarsofthouse_reepay_customer->addColumn(
            'magento_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'customer email'
        );

        $table_radarsofthouse_reepay_customer->addColumn(
            'handle',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'reepay customer handle'
        );

        $setup->getConnection()->createTable($table_radarsofthouse_reepay_customer);

    }
}
