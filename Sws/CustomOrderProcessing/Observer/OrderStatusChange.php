<?php
declare(strict_types=1);

namespace Sws\CustomOrderProcessing\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

/**
 * Class OrderStatusChange
 * Logs order status changes and sends email notification on order shipment
 */
class OrderStatusChange implements ObserverInterface
{
    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * OrderStatusChange constructor.
     * @param ResourceConnection $resource
     * @param LoggerInterface $logger
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        ResourceConnection $resource,
        LoggerInterface    $logger,
        TransportBuilder   $transportBuilder
    )
    {
        $this->resource = $resource;
        $this->logger = $logger;
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * Execute method triggered on order status change event
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $orderId = $order->getId();
        $oldStatus = $order->getOrigData('status') ?? $order->getStatus();
        $newStatus = $order->getStatus();

        // Log status change
        $connection = $this->resource->getConnection();
        $tableName = $this->resource->getTableName('sws_order_status_log');

        $connection->insert($tableName, [
            'order_id' => $orderId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s')
        ]);

        // Send email if order is marked as shipped
        if ($order->hasShipments()) {
            try {
                $transport = $this->transportBuilder
                    ->setTemplateIdentifier('order_shipped_email_template')
                    ->setTemplateOptions([
                        'area' => 'frontend',
                        'store' => $order->getStoreId(),
                    ])
                    ->setTemplateVars(['order' => $order])
                    ->setFrom(['name' => 'Store Name', 'email' => 'sales@website.com'])
                    ->addTo($order->getCustomerEmail())
                    ->getTransport();
                $transport->sendMessage();
            } catch (\Exception $e) {
                $this->logger->error('Email sending failed: ' . $e->getMessage());
            }
        }
    }
}
