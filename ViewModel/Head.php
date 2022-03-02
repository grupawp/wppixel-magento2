<?php

declare(strict_types=1);

namespace WP\Pixel\ViewModel;

use WP\Pixel\Helper\Config;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class Head
 * @package WP\Pixel\ViewModel
 */
class Head implements ArgumentInterface
{
    /**
     * @var Config
     */
    private $configHelper;

    /**
     * Head constructor.
     * @param Config $configHelper
     */
    public function __construct(
        Config $configHelper
    ) {
        $this->configHelper = $configHelper;
    }

    /**
     * @return string|null
     */
    public function getWPPixelId(): ?string
    {
        return $this->configHelper->getWPPixelId();
    }
}
