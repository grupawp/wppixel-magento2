<?php

declare(strict_types=1);

namespace WP\Pixel\Plugin;

use Magento\Customer\Controller\Account\LoginPost as Subject;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\Redirect;

/**
 * Class AfterCustomerSignIn
 * @package WP\Pixel\Plugin
 */
class AfterCustomerSignIn
{
    public const KEY = 'after_sign_in';

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * AfterContactUsFormSent constructor.
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * @param Subject $subject
     * @param Redirect $result
     * @return Redirect
     */
    public function afterExecute(Subject $subject, Redirect $result): Redirect
    {
        $this->dataPersistor->set(self::KEY, true);

        return $result;
    }
}