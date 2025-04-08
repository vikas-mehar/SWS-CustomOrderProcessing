<?php
declare(strict_types=1);

namespace Sws\CustomOrderProcessing\Model\ResourceModel\OrderStatusLog;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Sws\CustomOrderProcessing\Model\OrderStatusLog as OrderStatusLogModel;
use Sws\CustomOrderProcessing\Model\ResourceModel\OrderStatusLog;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'log_id';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            OrderStatusLogModel::class,
            OrderStatusLog::class
        );
    }
}

