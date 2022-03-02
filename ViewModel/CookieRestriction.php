<?php

declare(strict_types=1);

namespace WP\Pixel\ViewModel;

use WP\Pixel\Api\ContentRepositoryInterface;
use WP\Pixel\Helper\Config;
use Magento\Cookie\Helper\Cookie;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class CookieRestriction
 * @package WP\Pixel\ViewModel
 */
class CookieRestriction extends AbstractTrack
{
    /**
     * @var Cookie
     */
    private $cookieHelper;

    /**
     * CookieRestriction constructor.
     * @param Config $configHelper
     * @param RequestInterface $request
     * @param ContentRepositoryInterface $contentRepository
     * @param SerializerInterface $serializer
     * @param Cookie $cookieHelper
     */
    public function __construct(
        Config $configHelper,
        RequestInterface $request,
        ContentRepositoryInterface $contentRepository,
        SerializerInterface $serializer,
        Cookie $cookieHelper
    ) {
        parent::__construct(
            $configHelper,
            $request,
            $contentRepository,
            $serializer
        );
        $this->cookieHelper = $cookieHelper;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool)$this->cookieHelper->isCookieRestrictionModeEnabled();
    }
}
