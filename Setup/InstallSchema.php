<?php

namespace Sundial\M2GTID\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable(''))
            ->addColumn(
                'ID?',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID?'
            )
            ->addColumn(
                '',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                ''
            )
********TABLE DESC TO BE DETERMINED LATER********
            ->addIndex(
                $installer->getIdxName('', ['']),
                ['']
            )
          
            ->setComment('Sundial M2 GTID');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
