<?php

declare(strict_types=1);

namespace WP\Pixel\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Config
 * @package WP\Pixel\Helper
 */
class Config extends AbstractHelper
{
    private const PREFIX = 'wp_pixel/general/';

    private const XML_PATH_ENABLED = self::PREFIX . 'enabled';

    private const XML_PATH_WP_PIXEL_ID = self::PREFIX . 'wp_pixel_id';

    private const XML_PATH_EAN_ATTRIBUTE_CODE = self::PREFIX . 'ean_attribute_code';

    private const XML_PATH_VIEW_TRACKING_ENABLED = self::PREFIX . 'view_tracking_enabled';

    private const XML_PATH_VIEW_PRODUCT_TRACKING_ENABLED = self::PREFIX . 'view_product_tracking_enabled';

    private const XML_PATH_PRODUCT_LIST_TRACKING_ENABLED = self::PREFIX . 'product_list_tracking_enabled';

    private const XML_PATH_ADD_TO_CART_TRACKING_ENABLED = self::PREFIX . 'add_to_cart_tracking_enabled';

    private const XML_PATH_PURCHASE_TRACKING_ENABLED = self::PREFIX . 'purchase_tracking_enabled';

    private const XML_PATH_REGISTER_FORM_TRACKING_ENABLED = self::PREFIX . 'register_form_tracking_enabled';

    private const XML_PATH_LOGIN_TRACKING_ENABLED = self::PREFIX . 'login_tracking_enabled';

    private const XML_PATH_CONTACT_FORM_TRACKING_ENABLED = self::PREFIX . 'contact_form_tracking_enabled';

    private const XML_PATH_SIZE_ATTRIBUTES_IDS = self::PREFIX . 'size_attributes_ids';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Config constructor.
     * @param StoreManagerInterface $storeManager
     * @param Context $context
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Context $context
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isViewTrackingEnabled(): bool
    {
        return $this->isEnabled() && $this->scopeConfig->isSetFlag(
            self::XML_PATH_VIEW_TRACKING_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isViewProductTrackingEnabled(): bool
    {
        return $this->isEnabled() && $this->scopeConfig->isSetFlag(
            self::XML_PATH_VIEW_PRODUCT_TRACKING_ENABLED,
                ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isProductListTrackingEnabled(): bool
    {
        return $this->isEnabled() && $this->scopeConfig->isSetFlag(
            self::XML_PATH_PRODUCT_LIST_TRACKING_ENABLED,
                ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isAddToCartTrackingEnabled(): bool
    {
        return $this->isEnabled() && $this->scopeConfig->isSetFlag(
            self::XML_PATH_ADD_TO_CART_TRACKING_ENABLED,
                ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isPurchaseTrackingEnabled(): bool
    {
        return $this->isEnabled() && $this->scopeConfig->isSetFlag(
            self::XML_PATH_PURCHASE_TRACKING_ENABLED,
                ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isRegisterFormTrackingEnabled(): bool
    {
        return $this->isEnabled() && $this->scopeConfig->isSetFlag(
            self::XML_PATH_REGISTER_FORM_TRACKING_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isLoginTrackingEnabled(): bool
    {
        return $this->isEnabled() && $this->scopeConfig->isSetFlag(
            self::XML_PATH_LOGIN_TRACKING_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isContactFormTrackingEnabled(): bool
    {
        return $this->isEnabled() && $this->scopeConfig->isSetFlag(
            self::XML_PATH_CONTACT_FORM_TRACKING_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
    }

    /**
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getWPPixelId(): ?string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_WP_PIXEL_ID,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
    }

    /**
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getEANAttributeCode(): ?string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EAN_ATTRIBUTE_CODE,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getSizeAttributesIds(): array
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_SIZE_ATTRIBUTES_IDS,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );

        return $value ? explode(',', $value) : [];
    }
}
