<?php

declare(strict_types=1);

namespace WP\Pixel\Model;

use WP\Pixel\Api\ContentRepositoryInterface;
use WP\Pixel\Helper\Config;
use WP\Pixel\Observer\CheckoutCartProductAddAfter;
use WP\Pixel\Plugin\AfterCustomerSignIn;
use Magento\Bundle\Model\Product\Type as Bundle;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Session\SessionManager;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class ContentRepository
 * @package WP\Pixel\Model
 */
class ContentRepository implements ContentRepositoryInterface
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var Config
     */
    private $configHelper;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * ProductContentRepository constructor.
     * @param SerializerInterface $serializer
     * @param ProductRepositoryInterface $productRepository
     * @param Config $configHelper
     * @param CategoryRepositoryInterface $categoryRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param OrderRepositoryInterface $orderRepository
     * @param DataPersistorInterface $dataPersistor
     * @param SessionManager $customerSession
     */
    public function __construct(
        SerializerInterface $serializer,
        ProductRepositoryInterface $productRepository,
        Config $configHelper,
        CategoryRepositoryInterface $categoryRepository,
        PriceCurrencyInterface $priceCurrency,
        OrderRepositoryInterface $orderRepository,
        DataPersistorInterface $dataPersistor,
        SessionManager $customerSession
    ) {
        $this->serializer = $serializer;
        $this->productRepository = $productRepository;
        $this->configHelper = $configHelper;
        $this->categoryRepository = $categoryRepository;
        $this->priceCurrency = $priceCurrency;
        $this->orderRepository = $orderRepository;
        $this->dataPersistor = $dataPersistor;
        $this->customerSession = $customerSession;
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getAddToCartProductContent(): string
    {
        $data = $this->dataPersistor->get(CheckoutCartProductAddAfter::KEY_ADD_TO_CART_AFTER);
        $this->dataPersistor->clear(CheckoutCartProductAddAfter::KEY_ADD_TO_CART_AFTER);

        $content = $this->getProductById($data->getProductId());
        $orderOptions = $data->getOrderOptions();
        $content['quantity'] = $data->getQtyToAdd();
        $content['sizes'] = $this->getSelectedSize($orderOptions);
        $content['price'] = $data->getItemPrice();

        return $this->serializer->serialize($this->filterEmptyValues($content));
    }

    /**
     * @param int $productId
     * @param int|null $categoryId
     * @return array
     * @throws NoSuchEntityException
     */
    public function getProductById(int $productId, ?int $categoryId = null): array
    {
        $product = $this->getProduct($productId);

        return $this->filterEmptyValues([
            'id' => $this->getSku($product),
            'name' => $this->getName($product),
            'category' => $this->getCategoryName($product),
            'price' => $this->getPrice($product),
            'in_stock' => $this->isInStock($product),
            'sizes' => $this->getSizes($product),
            'ean' => $this->getEan($product),
            'weight' => $this->getWeight($product),
        ]);
    }

    /**
     * @param int $orderId
     * @return string
     * @throws NoSuchEntityException
     */
    public function getOrder(int $orderId): string
    {
        $order = $this->orderRepository->get($orderId);
        $data = $this->getItemData($order);

        $data = $this->filterEmptyValues([
            'transaction_id' => $order->getIncrementId(),
            'value' => $this->getOrderTotal($order),
            'value_gross' => $this->getOrderTotalGross($order),
            'shipping_cost' => $this->priceCurrency->convertAndRound($order->getShippingAmount()),
            'discount_code' => $order->getCouponCode(),
            'contents' => $data,
        ]);

        return $this->serializer->serialize($data);
    }

    /**
     * @return bool
     */
    public function isAfterSignIn(): bool
    {
        $isAfterSuccessfulSignIn = (bool)($this->dataPersistor->get(AfterCustomerSignIn::KEY)
            && $this->customerSession->isLoggedIn());
        $this->dataPersistor->clear(AfterCustomerSignIn::KEY);

        return $isAfterSuccessfulSignIn;
    }

    /**
     * @param OrderInterface $order
     * @return float
     */
    private function getOrderTotal(OrderInterface $order): float
    {
        return (float)($order->getGrandTotal() - $order->getTaxAmount()) - $order->getShippingAmount();
    }

    /**
     * @param OrderInterface $order
     * @return float
     */
    private function getOrderTotalGross(OrderInterface $order): float
    {
        return (float)$order->getGrandTotal() - $order->getShippingAmount();
    }

    /**
     * @param OrderInterface $order
     * @return array
     * @throws NoSuchEntityException
     */
    private function getItemData(OrderInterface $order): array
    {
        $data = [];

        /** @var OrderItemInterface $item */
        foreach ($order->getAllVisibleItems() as $item) {
            $options = $item->getProductOptions();
            $itemData = $this->getProductById((int)$item->getProductId());
            $itemData['sizes'] = $this->getSelectedSize($options);
            $itemData['price'] = $this->priceCurrency->convertAndRound($item->getPrice());
            $itemData['quantity'] = (int)$item->getQtyOrdered();
            $data[] = $itemData;
        }

        return $data;
    }

    /**
     * @param ProductInterface $product
     * @return string[]
     * @throws NoSuchEntityException
     */
    private function getSizes(ProductInterface $product): array
    {
        if ($product->getTypeId() !== Configurable::TYPE_CODE) {
            return [];
        }

        $sizeAttributesIds = $this->configHelper->getSizeAttributesIds();
        $options = $product->getTypeInstance()->getConfigurableAttributesAsArray($product);

        foreach ($options as $optionId => $option) {
            if (!in_array($optionId, $sizeAttributesIds)) {
                continue;
            }
            $sizes = [];
            foreach ($option['values'] as $value) {
                $sizes[] = $value['label'];
            }
            return $sizes;
        }

        return [];
    }

    /**
     * @param array $orderOptions
     * @return array|null
     * @throws NoSuchEntityException
     */
    private function getSelectedSize(array $orderOptions): ?array
    {
        $sizeAttributesIds = $this->configHelper->getSizeAttributesIds();

        if (!isset($orderOptions['info_buyRequest']['super_attribute'])) {
            return null;
        }

        foreach ($orderOptions['info_buyRequest']['super_attribute'] as $attributeId => $optionId) {
            if (in_array($attributeId, $sizeAttributesIds)) {
                foreach ($orderOptions['attributes_info'] as $attribute) {
                    if ($attribute['option_value'] == $optionId) {
                        return [$attribute['value']];
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param ProductInterface $product
     * @return string
     */
    private function getSku(ProductInterface $product): string
    {
        return $product->getSku();
    }

    /**
     * @param ProductInterface $product
     * @return string
     */
    private function getName(ProductInterface $product): string
    {
        return $product->getName();
    }

    /**
     * @param ProductInterface $product
     * @return float
     */
    private function getPrice(ProductInterface $product): float
    {
        if (in_array($product->getTypeId(), [Bundle::TYPE_CODE, Grouped::TYPE_CODE])) {
            $priceInfo = $product->getPriceInfo()->getPrice('final_price');
            $minimalPrice =  $priceInfo->getMinimalPrice()->getValue();

            return $this->priceCurrency->convertAndRound($minimalPrice);
        }

        return $this->priceCurrency->convertAndRound($product->getFinalPrice());
    }

    /**
     * @param ProductInterface $product
     * @return bool
     */
    private function isInStock(ProductInterface $product): bool
    {
        return $product->isSaleable();
    }

    /**
     * @param ProductInterface $product
     * @return string|null
     * @throws NoSuchEntityException
     */
    private function getEan(ProductInterface $product): ?string
    {
        $eanAttributeCode = $this->configHelper->getEANAttributeCode();

        if (empty($eanAttributeCode)) {
            return null;
        }

        return $product->getData($eanAttributeCode);
    }

    /**
     * @param ProductInterface $product
     * @return float|null
     */
    private function getWeight(ProductInterface $product): ?float
    {
        return (float)$product->getWeight();
    }

    /**
     * @param ProductInterface $product
     * @return string|null
     * @throws NoSuchEntityException
     */
    private function getCategoryName(ProductInterface $product): ?string
    {
        $categoryIds = $product->getCategoryIds();

        $selectedCategory = null;
        foreach ($categoryIds as $categoryId) {
            $category = $this->categoryRepository->get($categoryId);
            if ($category->getChildrenCount() == 0
                && $this->isPathActive($category)
                && ($selectedCategory === null
                    || $selectedCategory->getProductCount() <= $category->getProductCount()
                        && $category->getId() < $selectedCategory->getId()
                )) {
                $selectedCategory = $category;
            }
        }

        return $selectedCategory->getName();
    }

    /**
     * @param CategoryInterface $category
     * @return bool
     * @throws NoSuchEntityException
     */
    private function isPathActive(CategoryInterface $category): bool
    {
        $parentIds = $category->getParentIds();
        array_shift($parentIds);

        foreach ($parentIds as $parentId) {
            $parentCategory = $this->categoryRepository->get($parentId);
            if (!$parentCategory->getIsActive()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param int $productId
     * @return ProductInterface|null
     */
    private function getProduct(int $productId): ProductInterface
    {
        try {
            return $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * @param string[] $productData
     * @return string[]
     */
    private function filterEmptyValues(array $productData): array
    {
        $filteredProductData = [];

        foreach ($productData as $key => $value) {
            if (!empty($value)) {
                $filteredProductData[$key] = $value;
            }
        }

        return $filteredProductData;
    }
}
