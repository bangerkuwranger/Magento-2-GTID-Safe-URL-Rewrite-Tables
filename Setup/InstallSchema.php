<?php
/**
 * Copyright © 2017 Chad A. Carino. All rights reserved.
 * See LICENSE file for license details.
 *
 * @package    Bangerkuwranger/GtidSafeUrlRewriteTables
 * @author     Chad A. Carino <artist@chadacarino.com>
 * @author     Burak Bingollu <burak.bingollu@gmail.com>
 * @copyright  2017 Chad A. Carino
 * @license    https://opensource.org/licenses/MIT  MIT License
 */
namespace Bangerkuwranger\GtidSafeUrlRewriteTables\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

//set up table with foreign key set for transaction index... we'll then only drop rows from the transaction and drop transaction table.
        $transactiontable = $installer->getConnection()
        
        ;
        $keytable = $installer->getConnection()
            ->newTable($installer->getTable('Gtid_SafeUrl_Rewrite_Table'))
            ->addColumn(
                'url_rewrite_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'URL Rewrite ID'
            )
            ->addColumn(
                'gtidsafe_transaction_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['identity' => true, 'nullable' => false, ],
                'URL Rewrite ID'
            )
            ->addColumn(
                'url_rewrite',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['identity' => false, 'nullable' => false],
                'URL Rewrite'
            )
            ->addColumn(
                'hash_key',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['identity' => false, 'nullable' => false],
                'Hash Key'
            )
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => false, 'nullable' => false],
                'Entity ID'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => false, 'nullable' => false],
                'Store ID'
            )
            ->addIndex(
                $installer->getIdxName('Gtid_SafeUrl_Rewrite_Table', ['url_rewrite_id']),
                ['url_rewrite_id']
            )
            ->setComment('Gtid SafeUrl Rewrite Table');
        $installer->getConnection()->createTable($keytable);

        $installer->endSetup();
    }
}
