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
namespace Bangerkuwranger\GtidSafeUrlRewriteTables\Model\Rewrite\CatalogUrlRewrite\Map;

use Magento\Framework\Phrase;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\ResourceConnection;
use Magento\UrlRewrite\Model\MergeDataProvider;
use Magento\CatalogUrlRewrite\Model\Map\HashMapPool;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Map that holds data for category url rewrites entity
 */

class DataProductUrlRewriteDatabaseMap implements DatabaseMapInterface
{
    /**
     * Logging instance
     * @var \Bangerkuwranger\GtidSafeUrlRewriteTables\Logger\Logger
     */
    protected $_bklogger;
    
    /**
     * Entity type for queries.
     *
     * @var string
     */
    private $entityType = 'product';

    /**
     * Names of the temporary tables.
     *
     * @var string[]
     */
//     private $createdTableAdapters = [];

	/**
     * Name of the map table.
     *
     * @var string
     */
     private $mapTableName = 'Gtid_SafeUrl_Rewrite_Table';

    /**
     * Pool for hash maps.
     *
     * @var HashMapPool
     */
    private $hashMapPool;

    /**
     * Resource connection.
     *
     * @var ResourceConnection
     */
    private $connection;

    /**
     * @param ResourceConnection $connection
     * @param HashMapPool $hashMapPool
     * @param \Bangerkuwranger\GtidSafeUrlRewriteTables\Logger\Logger $logger
     */
    public function __construct(
        ResourceConnection $connection,
        HashMapPool $hashMapPool,
        \Bangerkuwranger\GtidSafeUrlRewriteTables\Logger\Logger $logger
    ) {
        $this->connection = $connection;
        $this->hashMapPool = $hashMapPool;
        $this->_bklogger = $logger;
    }

    /**
     * Deprecated by design from this override class... 
     * Throws an exception... was:
     * Generates data from categoryId and stores it into a temporary table.
     *
     * @param int $categoryId
     * @return void
     */
    private function generateTableAdapter($categoryId)
    {
        $errorPhrase = new Phrase('Method generateTableAdapter not found. Developers have missed a dependency. Please alert dev team ASAP.');
        throw new NotFoundException($errorPhrase);
        return;
    }
    
    /**
     * Deprecated by design from this override class... 
     * Throws an exception... was:
     * Destroys data in the temporary table by categoryId.
     * It also destroys the data in other maps that are dependencies used to construct the data.
     *
     *
     * @param int $categoryId
     * @return void
     */
    private function destroyTableAdapter($categoryId)
    {
        $errorPhrase = new Phrase('Method destroyTableAdapter not found. Developers have missed a dependency. Please alert dev team ASAP.');
        throw new NotFoundException($errorPhrase);
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($categoryId, $key)
    {
//         $this->generateTableAdapter($categoryId);
		$this->generateData($categoryId);
        $urlRewritesGetDataConnection = $this->connection->getConnection();
        $select = $urlRewritesGetDataConnection->select()
            ->from(['e' => $this->connection->getTableName($this->mapTableName)])
            ->where('hash_key = ?', $key);
        return $urlRewritesGetDataConnection->fetchAll($select);
    }

    /**
     * Queries the database for all category url rewrites that are affected by the category identified by $categoryId and saves it to the table.
     * (this should probably use transactions for concurrent edits to products or cats by many users...)
     *
     * @param int $categoryId
     * @return void
     */
    private function generateData($categoryId)
    {
        $urlRewritesGenerateDataConnection = $this->connection->getConnection();
        $select = $urlRewritesGenerateDataConnection->select()
            ->from(
                ['e' => $this->connection->getTableName('url_rewrite')],
                ['e.*', 'hash_key' => new \Zend_Db_Expr(
                    "CONCAT(e.store_id,'" . MergeDataProvider::SEPARATOR . "', e.entity_id)"
                )
                ]
            )
            ->where('entity_type = ?', $this->entityType)
            ->where(
                $urlRewritesGenerateDataConnection->prepareSqlCondition(
                    'entity_id',
                    [
                        'in' => $this->hashMapPool->getDataMap(DataProductHashMap::class, $categoryId)
                            ->getAllData($categoryId)
                    ]
                )
            );
            
        // $mapName = $this->temporaryTableService->createFromSelect(
//             $select,
//             $this->connection->getConnection(),
//             [
//                 'PRIMARY' => ['url_rewrite_id'],
//                 'HASHKEY_ENTITY_STORE' => ['hash_key'],
//                 'ENTITY_STORE' => ['entity_id', 'store_id']
//             ]
//         );
			$tempRewriteBinding = $select->getBind();
			$this->_bklogger->prettyLog('binding: ' . $tempRewriteBinding);
			$urlRewritesGenerateDataConnection->insert( $this->connection->getTableName( $this->mapTableName ), $tempRewriteBinding );
			
//         return $mapName;
			return;
    }

    /**
     * {@inheritdoc}
     */
    public function destroyMapTableData($categoryId)
    {
        $this->hashMapPool->resetMap(DataProductHashMap::class, $categoryId);
        $urlRewritesDestroyMapTableDataConnection = $this->connection->getConnection();
		$urlRewritesDestroyMapTableDataConnection->query('TRUNCATE TABLE `' . $this->connection->getTableName( $this->mapTableName ) . '`' );
    }
}
