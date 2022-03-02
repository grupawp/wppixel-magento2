<?php

declare(strict_types=1);

namespace WP\Pixel\ViewModel;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Login
 * @package WP\Pixel\ViewModel
 */
class Login extends AbstractTrack
{
    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isEnabled(): bool
    {
        return $this->configHelper->isLoginTrackingEnabled();
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
        return 'Login';
    }
}
