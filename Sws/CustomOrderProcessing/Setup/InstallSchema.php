<?php

namespace Sws\CustomOrderProcessing\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $table = $setup->getConnection()->newTable($setup->getTable('sws_order_status_log'))
            ->addColumn('log_id', Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true])
            ->addColumn('order_id', Table::TYPE_INTEGER, null, ['nullable' => false])
            ->addColumn('old_status', Table::TYPE_TEXT, 50, ['nullable' => false])
            ->addColumn('new_status', Table::TYPE_TEXT, 50, ['nullable' => false])
            ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT]);

        $setup->getConnection()->createTable($table);
        $setup->endSetup();
    }
}
