<?php

declare(strict_types=1);

namespace WP\Pixel\ViewModel;

use WP\Pixel\Api\ContentRepositoryInterface;
use WP\Pixel\Helper\Config;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class Purchase
 * @package WP\Pixel\ViewModel
 */
class Purchase extends AbstractTrack
{
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * Purchase constructor.
     * @param Config $configHelper
     * @param RequestInterface $request
     * @param ContentRepositoryInterface $productContentRepository
     * @param SerializerInterface $serializer
     * @param Session $checkoutSession
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Config $configHelper,
        RequestInterface $request,
        ContentRepositoryInterface $productContentRepository,
        SerializerInterface $serializer,
        Session $checkoutSession,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct(
            $configHelper,
            $request,
            $productContentRepository,
            $serializer
        );
        $this->checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isEnabled(): bool
    {
        return $this->configHelper->isPurchaseTrackingEnabled();
    }

    /**
     * @return string
     */
    public function getTrackName(): string
    {
        return 'Purchase';
    }

    /**
     * @param int|null $productId
     * @param int|null $categoryId
     * @return string
     */
    public function getContents(?int $productId = null, ?int $categoryId = null): string
    {
        $orderId = (int)$this->checkoutSession->getLastOrderId();

        return $this->contentRepository->getOrder($orderId);
    }
}
