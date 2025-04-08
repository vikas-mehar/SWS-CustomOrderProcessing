<?php
declare(strict_types=1);

namespace Sws\CustomOrderProcessing\Api\Data;

interface OrderStatusLogInterface
{
    const NEW_STATUS = 'new_status';
    const OLD_STATUS = 'old_status';
    const ORDER_ID = 'order_id';
    const CREATED_AT = 'created_at';
    const LOG_ID = 'log_id';

    /**
     * Get log_id
     * @return string|null
     */
    public function getLogId();

    /**
     * Set log_id
     * @param string $logId
     * @return \Sws\CustomOrderProcessing\OrderStatusLog\Api\Data\OrderStatusLogInterface
     */
    public function setLogId($logId);

    /**
     * Get order_id
     * @return string|null
     */
    public function getOrderId();

    /**
     * Set order_id
     * @param string $orderId
     * @return \Sws\CustomOrderProcessing\OrderStatusLog\Api\Data\OrderStatusLogInterface
     */
    public function setOrderId($orderId);

    /**
     * Get old_status
     * @return string|null
     */
    public function getOldStatus();

    /**
     * Set old_status
     * @param string $oldStatus
     * @return \Sws\CustomOrderProcessing\OrderStatusLog\Api\Data\OrderStatusLogInterface
     */
    public function setOldStatus($oldStatus);

    /**
     * Get new_status
     * @return string|null
     */
    public function getNewStatus();

    /**
     * Set new_status
     * @param string $newStatus
     * @return \Sws\CustomOrderProcessing\OrderStatusLog\Api\Data\OrderStatusLogInterface
     */
    public function setNewStatus($newStatus);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Sws\CustomOrderProcessing\OrderStatusLog\Api\Data\OrderStatusLogInterface
     */
    public function setCreatedAt($createdAt);
}

