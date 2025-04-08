<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Sws\CustomOrderProcessing\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface OrderStatusLogRepositoryInterface
{

    /**
     * Retrieve OrderStatusLog
     * @param string $orderstatuslogId
     * @return \Sws\CustomOrderProcessing\Api\Data\OrderStatusLogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($orderstatuslogId);

    /**
     * Retrieve OrderStatusLog matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Sws\CustomOrderProcessing\Api\Data\OrderStatusLogSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );
}

