<?php

declare(strict_types=1);

namespace WP\Pixel\ViewModel;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class ViewProduct
 * @package WP\Pixel\ViewModel
 */
class ViewProduct extends AbstractTrack
{
    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isEnabled(): bool
    {
        return $this->configHelper->isViewProductTrackingEnabled();
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
        return 'ViewProduct';
    }
}
