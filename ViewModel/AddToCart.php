<?php

declare(strict_types=1);

namespace WP\Pixel\ViewModel;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class AddToCart
 * @package WP\Pixel\ViewModel
 */
class AddToCart extends AbstractTrack
{
    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isEnabled(): bool
    {
        return $this->configHelper->isAddToCartTrackingEnabled();
    }

    /**
     * @return string
     */
    public function getTrackName(): string
    {
        return 'AddToCart';
    }
}
