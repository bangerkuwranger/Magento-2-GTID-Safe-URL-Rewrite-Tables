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

/**
 * Interface for a mysql data type of a map.
 *
 * Is used to get data by a unique key from a mapping table in mysql to prevent memory usage.
 * It internally store the creation of the actual data and it initializes itself when we call getData.
 * We should always call destroyMapTableData when we don't need the mapping tables anymore.
 */

interface DatabaseMapInterface
{
    /**
     * Gets data by key from a map identified by a category Id.
     *
     * The key is a unique identifier that matches the values of the index used to build the temporary table.
     *
     * Example "1_2" where ids would correspond to store_id entity_id
     *
     * @param int $categoryId
     * @param string $key
     * @return array
     */
    public function getData($categoryId, $key);

    /**
     * Destroys data in the temporary table by categoryId.
     * It also destroys the data in other maps that are dependencies used to construct the data.
     *
     * @param int $categoryId
     * @return void
     */
//     public function destroyTableAdapter($categoryId);

	 /**
     * Destroys data in the mapping table by categoryId.
     * It also destroys the data in other maps that are dependencies used to construct the data.
     *
     * @param int $categoryId
     * @return void
     */
	public function destroyMapTableData($categoryId);
}
