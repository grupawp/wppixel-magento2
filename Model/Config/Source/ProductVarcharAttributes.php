<?php

declare(strict_types=1);

namespace WP\Pixel\Model\Config\Source;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class TextAttribute
 * @package WP\Pixel\Model\Config\Source
 */
class ProductVarcharAttributes implements OptionSourceInterface
{
    private const CATALOG_PRODUCT_ENTITY_TYPE_ID = 4;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * TextAttribute constructor.
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()->from(
            ['main_table' => $connection->getTableName('eav_attribute')],
            ['value' => 'attribute_code', 'label' => 'frontend_label']
            )->where('entity_type_id = ?', self::CATALOG_PRODUCT_ENTITY_TYPE_ID)
            ->where('backend_type = ?', 'varchar');

        return array_merge([[
            'value' => '',
            'label' => __('-- Select --')
        ]], $connection->fetchAll($select));
    }
}
