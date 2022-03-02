<?php

declare(strict_types=1);

namespace WP\Pixel\Plugin;

use Magento\Contact\Controller\Index\Post as Subject;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\Redirect;

/**
 * Class AfterContactFormSent
 * @package WP\Pixel\Plugin
 */
class AfterContactFormSent
{
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
        if (empty($this->dataPersistor->get('contact_us'))) {
            $this->dataPersistor->set('contact_us_sent', true);
        }

        return $result;
    }
}
