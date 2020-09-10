<?php

namespace Radarsofthouse\Reepay\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), "1.0.14", "<")) {
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
        }
    }
}
