<?php

declare(strict_types=1);

namespace WP\Pixel\ViewModel;

use WP\Pixel\Api\ContentRepositoryInterface;
use WP\Pixel\Helper\Config;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class AbstractTrack
 * @package WP\Pixel\ViewModel
 */
abstract class AbstractTrack implements ArgumentInterface
{
    private const PARAM_PRODUCT_ID = 'id';

    private const PARAM_CATEGORY_ID = 'id';

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * @var ContentRepositoryInterface
     */
    protected $contentRepository;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * AbstractTrack constructor.
     * @param Config $configHelper
     * @param RequestInterface $request
     * @param ContentRepositoryInterface $contentRepository
     * @param SerializerInterface $serializer
     */
    public function __construct(
        Config $configHelper,
        RequestInterface $request,
        ContentRepositoryInterface $contentRepository,
        SerializerInterface $serializer
    ) {
        $this->configHelper = $configHelper;
        $this->request = $request;
        $this->contentRepository = $contentRepository;
        $this->serializer = $serializer;
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isEnabled(): bool
    {
        return $this->configHelper->isEnabled();
    }

    /**
     * @return string|null
     */
    public function getTractName(): ?string
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function getContentName(): ?string
    {
        return null;
    }

    /**
     * @param int|null $productId
     * @param int|null $categoryId
     * @return string[]
     */
    public function getContents(?int $productId = null, ?int $categoryId = null): string
    {
        if (empty($productId)) {
            $productId = (int)$this->request->getParam(self::PARAM_PRODUCT_ID);
        }

        return $this->serializer->serialize($this->contentRepository->getProductById($productId, $categoryId));
    }
}
