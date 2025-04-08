<?php
declare(strict_types=1);

namespace Sws\CustomOrderProcessing\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface OrderStatusLogSearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get OrderStatusLog list.
     * @return \Sws\CustomOrderProcessing\Api\Data\OrderStatusLogInterface[]
     */
    public function getItems();

    /**
     * Set log_id list.
     * @param \Sws\CustomOrderProcessing\Api\Data\OrderStatusLogInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

