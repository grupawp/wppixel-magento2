<?php

declare(strict_types=1);

namespace WP\Pixel\ViewModel;

use WP\Pixel\Api\ContentRepositoryInterface;
use WP\Pixel\Helper\Config;
use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\LayoutInterface;

/**
 * Class ProductList
 * @package WP\Pixel\ViewModel
 */
class ProductList extends AbstractTrack
{
    private const PARAM_CATEGORY_ID = 'id';

    private const PARAM_PAGE = 'p';

    private const DEFAULT_PAGE = 1;

    private const DEFAULT_LIMIT = 9;

    /**
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * ProductList constructor.
     * @param Config $configHelper
     * @param RequestInterface $request
     * @param ContentRepositoryInterface $productContentRepository
     * @param SerializerInterface $serializer
     * @param LayoutInterface $layout
     */
    public function __construct(
        Config $configHelper,
        RequestInterface $request,
        ContentRepositoryInterface $productContentRepository,
        SerializerInterface $serializer,
        LayoutInterface $layout
    ) {
        parent::__construct(
            $configHelper,
            $request,
            $productContentRepository,
            $serializer
        );
        $this->layout = $layout;
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isEnabled(): bool
    {
        return $this->configHelper->isProductListTrackingEnabled();
    }

    /**
     * @return string
     */
    public function getTrackName(): string
    {
        return 'ViewContent';
    }

    /**
     * @return string
     */
    public function getContentName(): string
    {
        return 'ProductList';
    }

    /**
     * @param int|null $productId
     * @param int|null $categoryId
     * @return string
     */
    public function getContents(?int $productId = null, ?int $categoryId = null): string
    {
        $productData = [];
        $productIds = $this->getCategoryProductIds();
        $categoryId = (int)$this->request->getParam(self::PARAM_CATEGORY_ID);

        foreach ($productIds as $productId) {
            $productData[] = parent::getContents($productId, $categoryId);
        }

        return implode(',', $productData);
    }

    /**
     * @return int[]
     */
    protected function getCategoryProductIds(): array
    {
        $productIds = [];

        $categoryProductListBlock = $this->layout->getBlock('category.products.list');
        $productCollection = $categoryProductListBlock->getLoadedProductCollection();
        $productCollection->setCurPage($this->getCurrentPage())->setPageSize($this->getLimit());

        foreach ($productCollection as $product) {
            $productIds[] = (int)$product->getId();
        }

        return $productIds;
    }

    /**
     * @return int
     */
    protected function getLimit(): int
    {
        /** @var Toolbar $productListBlockToolbar */
        $productListBlockToolbar = $this->layout->getBlock('product_list_toolbar');

        if (empty($productListBlockToolbar)) {
            return self::DEFAULT_LIMIT;
        }

        return (int)$productListBlockToolbar->getLimit();
    }

    /**
     * @return int
     */
    protected function getCurrentPage(): int
    {
        $page = (int)$this->request->getParam(self::PARAM_PAGE);

        if (!$page) {
            return self::DEFAULT_PAGE;
        }

        return $page;
    }
}
