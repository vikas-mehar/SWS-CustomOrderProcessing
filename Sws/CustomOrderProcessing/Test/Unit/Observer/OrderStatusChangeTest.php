<?php
declare(strict_types=1);

namespace Sws\CustomOrderProcessing\Test\Unit\Observer;

use PHPUnit\Framework\TestCase;
use Sws\CustomOrderProcessing\Observer\OrderStatusChange;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\Mail\TransportInterface;
use Psr\Log\LoggerInterface;

class OrderStatusChangeTest extends TestCase
{
    private $resourceMock;
    private $loggerMock;
    private $transportBuilderMock;
    private $observerMock;
    private $eventMock;
    private $orderMock;
    private $dbConnectionMock;
    private $transportMock;

    private OrderStatusChange $observer;

    protected function setUp(): void
    {
        $this->resourceMock = $this->createMock(ResourceConnection::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->transportBuilderMock = $this->createMock(TransportBuilder::class);
        $this->orderMock = $this->createMock(Order::class);
        $this->observerMock = $this->createMock(Observer::class);
        $this->eventMock = $this->createMock(Event::class);
        $this->dbConnectionMock = $this->createMock(AdapterInterface::class);
        $this->transportMock = $this->createMock(TransportInterface::class);

        $this->observer = new OrderStatusChange(
            $this->resourceMock,
            $this->loggerMock,
            $this->transportBuilderMock
        );
    }

    public function testExecuteLogsOrderStatusChangeAndSendsEmail()
    {
        $orderId = 100;
        $oldStatus = 'pending';
        $newStatus = 'shipped';
        $customerEmail = 'customer@example.com';
        $storeId = 1;
        $tableName = 'sws_order_status_log';

        $eventStub = new class($this->orderMock) {
            private $order;
            public function __construct($order) {
                $this->order = $order;
            }
            public function getOrder() {
                return $this->order;
            }
        };

        $this->observerMock->method('getEvent')->willReturn($eventStub);

        $this->orderMock->method('getId')->willReturn($orderId);
        $this->orderMock->method('getOrigData')->with('status')->willReturn($oldStatus);
        $this->orderMock->method('getStatus')->willReturn($newStatus);
        $this->orderMock->method('hasShipments')->willReturn(true);
        $this->orderMock->method('getCustomerEmail')->willReturn($customerEmail);
        $this->orderMock->method('getStoreId')->willReturn($storeId);

        $this->resourceMock->method('getConnection')->willReturn($this->dbConnectionMock);
        $this->resourceMock->method('getTableName')->with('sws_order_status_log')->willReturn($tableName);

        $this->dbConnectionMock->expects($this->once())
            ->method('insert')
            ->with(
                $tableName,
                $this->callback(function ($data) use ($orderId, $oldStatus, $newStatus) {
                    return $data['order_id'] === $orderId &&
                        $data['old_status'] === $oldStatus &&
                        $data['new_status'] === $newStatus &&
                        isset($data['created_at']);
                })
            );

        $this->transportBuilderMock->expects($this->once())->method('setTemplateIdentifier')->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())->method('setTemplateOptions')->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())->method('setTemplateVars')->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())->method('setFrom')->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())->method('addTo')->with($customerEmail)->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())->method('getTransport')->willReturn($this->transportMock);
        $this->transportMock->expects($this->once())->method('sendMessage');

        $this->observer->execute($this->observerMock);
    }

    public function testExecuteHandlesEmailException()
    {
        $eventStub = new class($this->orderMock) {
            private $order;
            public function __construct($order) {
                $this->order = $order;
            }
            public function getOrder() {
                return $this->order;
            }
        };

        $this->observerMock->method('getEvent')->willReturn($eventStub);

        $this->orderMock->method('getId')->willReturn(1);
        $this->orderMock->method('getOrigData')->with('status')->willReturn('pending');
        $this->orderMock->method('getStatus')->willReturn('shipped');
        $this->orderMock->method('hasShipments')->willReturn(true);
        $this->orderMock->method('getCustomerEmail')->willReturn('fail@example.com');
        $this->orderMock->method('getStoreId')->willReturn(1);

        $this->resourceMock->method('getConnection')->willReturn($this->dbConnectionMock);
        $this->resourceMock->method('getTableName')->willReturn('sws_order_status_log');
        $this->dbConnectionMock->method('insert')->willReturn(true);

        $this->transportBuilderMock->method('setTemplateIdentifier')->willReturnSelf();
        $this->transportBuilderMock->method('setTemplateOptions')->willReturnSelf();
        $this->transportBuilderMock->method('setTemplateVars')->willReturnSelf();
        $this->transportBuilderMock->method('setFrom')->willReturnSelf();
        $this->transportBuilderMock->method('addTo')->willReturnSelf();
        $this->transportBuilderMock->method('getTransport')->willReturn($this->transportMock);

        $this->transportMock->method('sendMessage')->willThrowException(new \Exception('SMTP Failed'));

        $this->loggerMock->expects($this->once())->method('error')
            ->with($this->stringContains('Email sending failed: SMTP Failed'));

        $this->observer->execute($this->observerMock);
    }
}
