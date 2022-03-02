<?php

declare(strict_types=1);

namespace WP\Pixel\ViewModel;

use WP\Pixel\Api\ContentRepositoryInterface;
use WP\Pixel\Helper\Config;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class InputForm
 * @package WP\Pixel\ViewModel
 */
class InputForm extends AbstractTrack
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * InputForm constructor.
     * @param Config $configHelper
     * @param RequestInterface $request
     * @param ContentRepositoryInterface $contentRepository
     * @param SerializerInterface $serializer
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Config $configHelper,
        RequestInterface $request,
        ContentRepositoryInterface $contentRepository,
        SerializerInterface $serializer,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct(
            $configHelper,
            $request,
            $contentRepository,
            $serializer
        );
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isEnabled(): bool
    {
        return false === $this->isFormSentSuccessfully()
            && $this->configHelper->isContactFormTrackingEnabled();
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
        return 'InputForm';
    }

    /**
     * @return bool
     */
    private function isFormSentSuccessfully(): bool
    {
        return (bool)$this->dataPersistor->get('contact_us_sent');
    }
}
