<?php

namespace Sundial\M2GTID\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;


class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('sundial_m2_gtid'))
            ->addColumn(
                'url_rewrite_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'URL Rewrite ID'
            )
            ->addColumn(
                'url_rewrite',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['identity' => true, 'nullable' => false],
                'URL Rewrite'
            )

            
   ///    DOWN BELOW->     ********TABLE DESC TO BE DETERMINED LATER********
            
            

            ->addColumn(
                '',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                ''
            )

            
            
            ->addIndex(
                $installer->getIdxName('sundial_m2_gtid', ['url_rewrite_id']),
                ['url_rewrite_id']
            )
          
            ->setComment('Sundial M2 GTID');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
