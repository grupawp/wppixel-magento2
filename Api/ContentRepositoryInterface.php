<?php

declare(strict_types=1);

namespace WP\Pixel\Api;

use WP\Pixel\Api\Data\ProductInfoInterface;

/**
 * Interface ProductContentRepositoryInterface
 * @package WP\Pixel\Api
 */
interface ContentRepositoryInterface
{
    /**
     * @return string
     */
    public function getAddToCartProductContent(): string;

    /**
     * @param int $productId
     * @param int|null $categoryId
     * @return array
     */
    public function getProductById(int $productId, ?int $categoryId = null): array;

    /**
     * @param int $orderId
     * @return string
     */
    public function getOrder(int $orderId): string;

    /**
     * @return bool
     */
    public function isAfterSignIn(): bool;
}
