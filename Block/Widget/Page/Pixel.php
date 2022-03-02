<?php

declare(strict_types=1);

namespace WP\Pixel\Block\Widget\Page;

use Magento\Framework\View\Element\Html\Link;
use Magento\Widget\Block\BlockInterface;

/**
 * Class Pixel
 * @package WP\Pixel\Block\Widget\Page
 */
class Pixel extends Link implements BlockInterface
{
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
        return $this->getData('content_name');
    }
}
