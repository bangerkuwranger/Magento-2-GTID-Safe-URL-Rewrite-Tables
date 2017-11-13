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

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Phrase;
use Magento\Framework\Exception\NotFoundException;
use Magento\UrlRewrite\Model\MergeDataProvider;
use Magento\CatalogUrlRewrite\Model\Map\HashMapPool;

/**
 * Map that holds data for category url rewrites entity.
 */

class DataCategoryUrlRewriteDatabaseMap implements DatabaseMapInterface
{
    /**
     * Logging instance
     * @var \Bangerkuwranger\GtidSafeUrlRewriteTables\Logger\Logger
     */
    public $bklogger;
    
    /**
     * Entity type for queries.
     *
     * @var string
     */
    private $entityType = 'category';
    
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
     * @param HashMapPool $hashMapPool,
     * @param \Bangerkuwranger\GtidSafeUrlRewriteTables\Logger\Logger $logger
     */
    public function __construct(
        ResourceConnection $connection,
        HashMapPool $hashMapPool,
        \Bangerkuwranger\GtidSafeUrlRewriteTables\Logger\Logger $logger
    ) {
        $this->connection = $connection;
        $this->hashMapPool = $hashMapPool;
        $this->bklogger = $logger;
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
        $errorPhrase = new Phrase('Method not found. Developers have missed a dependency. Please alert dev team ASAP.');
        $this->bklogger->prettyLog($errorPhrase);
        throw new NotFoundException($errorPhrase);
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
        $this->bklogger->prettyLog($errorPhrase);
        throw new NotFoundException($errorPhrase);
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
                        'in' => array_merge(
                            $this->hashMapPool->getDataMap(DataCategoryUsedInProductsHashMap::class, $categoryId)
                                ->getAllData($categoryId),
                            $this->hashMapPool->getDataMap(DataCategoryHashMap::class, $categoryId)
                                ->getAllData($categoryId)
                        )
                    ]
                )
            );
        $tempRewriteBinding = $select->getBind();
        $this->bklogger->prettyLog('binding: ' . $tempRewriteBinding);
        //here's where we id the transaction and store to transaction table... return the ID and allow for the select to be processed back. Right now both product and category (this) models are falling back to the generic model method... which works, but there are no cached performance benefits leveraging mysql...
        $urlRewritesGenerateDataConnection->insert($this->connection->getTableName($this->mapTableName), $tempRewriteBinding);
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function destroyMapTableData($categoryId)
    {
        $this->hashMapPool->resetMap(DataCategoryUsedInProductsHashMap::class, $categoryId);
        $this->hashMapPool->resetMap(DataCategoryHashMap::class, $categoryId);
    }

    /**
     * Gets data by criteria from a map identified by a category Id.
     *
     * @param int $categoryId
     * @param string $key
     * @return array
     */
    public function getData($categoryId, $key)
    {
        $this->generateData($categoryId);
        $urlRewritesGetDataConnection = $this->connection->getConnection();
        $select = $urlRewritesGetDataConnection->select()->from(['e' => getTableName($this->mapTableName)]);
        if ($key !== "") {
            $select->where('hash_key = ?', $key);
        }

        return $urlRewritesGetDataConnection->fetchAll($select);
    }
}
