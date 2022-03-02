<?php

declare(strict_types=1);

namespace WP\Pixel\ViewModel;

use WP\Pixel\Api\ContentRepositoryInterface;
use WP\Pixel\Helper\Config;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\LayoutInterface;

/**
 * Class SearchList
 * @package WP\Pixel\ViewModel
 */
class SearchList extends ProductList
{
    /**
     * @return int[]
     */
    protected function getCategoryProductIds(): array
    {
        $productIds = [];

        $categoryProductListBlock = $this->layout->getBlock('search_result_list');
        $productCollection = $categoryProductListBlock->getLoadedProductCollection();

        foreach ($productCollection as $product) {
            $productIds[] = (int)$product->getId();
        }

        return $productIds;
    }
}
