<?php
declare(strict_types=1);

namespace Sws\CustomOrderProcessing\Api;

use Magento\Sales\Api\Data\OrderInterface;

interface OrderStatusInterface
{
    /**
     * Update order status via API
     *
     * @param string $incrementId
     * @param string $status
     * @return OrderInterface
     */
    public function updateStatus($incrementId, $status);
}
