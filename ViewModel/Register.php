<?php

declare(strict_types=1);

namespace WP\Pixel\ViewModel;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Register
 * @package WP\Pixel\ViewModel
 */
class Register extends AbstractTrack
{
    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isEnabled(): bool
    {
        return $this->configHelper->isRegisterFormTrackingEnabled();
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
        return 'Register';
    }
}
