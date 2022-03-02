<?php

declare(strict_types=1);

namespace WP\Pixel\Observer;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Quote\Model\Quote\Item;

/**
 * Class CheckoutCartProductAddAfter
 * @package WP\Pixel\Observer
 */
class CheckoutCartProductAddAfter implements ObserverInterface
{
    public const KEY_ADD_TO_CART_AFTER = 'add_to_cart_after';

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * AfterContactUsFormSent constructor.
     * @param DataPersistorInterface $dataPersistor
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(
        DataPersistorInterface $dataPersistor,
        DataObjectFactory $dataObjectFactory
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Observer for checkout_cart_product_add_after
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var Item $quoteItem */
        $quoteItem = $observer->getEvent()->getQuoteItem();
        /** @var ProductInterface $product */
        $product = $observer->getEvent()->getProduct();

        $orderOptions = $quoteItem->getProduct()->getTypeInstance(true)->getOrderOptions($quoteItem->getProduct());

        $dataObject = $this->dataObjectFactory->create();
        $dataObject->setProductId((int)$product->getId());
        $dataObject->setQtyToAdd((int)$quoteItem->getQtyToAdd());
        $dataObject->setOrderOptions($orderOptions);
        $dataObject->setItemPrice($quoteItem->getPrice());

        $this->dataPersistor->set(self::KEY_ADD_TO_CART_AFTER, $dataObject);
    }
}
