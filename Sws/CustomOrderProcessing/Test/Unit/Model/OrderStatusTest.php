<?php
declare(strict_types=1);

namespace Sws\CustomOrderProcessing\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use Sws\CustomOrderProcessing\Model\OrderStatus;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;

class OrderStatusTest extends TestCase
{
    private $orderRepositoryMock;
    private $orderMock;
    private OrderStatus $orderStatus;

    protected function setUp(): void
    {
        $this->orderRepositoryMock = $this->createMock(OrderRepositoryInterface::class);

        $this->orderMock = $this->getMockBuilder(OrderInterface::class)
            ->disableOriginalConstructor()
            ->addMethods(['getId', 'setStatus'])
            ->getMock();

        $this->orderStatus = new OrderStatus($this->orderRepositoryMock);
    }

    public function testUpdateStatusSuccessfullyUpdatesOrderStatus(): void
    {
        $incrementId = '100000001';
        $status = 'processing';

        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->with($incrementId)
            ->willReturn($this->orderMock);

        $this->orderMock->expects($this->once())
            ->method('getId')
            ->willReturn(101);

        $this->orderMock->expects($this->once())
            ->method('setStatus')
            ->with($status)
            ->willReturnSelf();

        $this->orderRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->orderMock);

        $result = $this->orderStatus->updateStatus($incrementId, $status);

        $this->assertSame($this->orderMock, $result);
    }

    public function testUpdateStatusThrowsNoSuchEntityExceptionWhenOrderNotFound(): void
    {
        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage('Order not found.');

        $this->orderRepositoryMock->expects($this->once())
            ->method('get')
            ->willThrowException(new NoSuchEntityException(__('Order not found.')));

        $this->orderStatus->updateStatus('999999', 'pending');
    }

    public function testUpdateStatusThrowsLocalizedExceptionOnGenericFailure(): void
    {
        $incrementId = '100000002';
        $status = 'complete';

        $this->orderRepositoryMock->method('get')->willReturn($this->orderMock);
        $this->orderMock->method('getId')->willReturn(102);
        $this->orderMock->method('setStatus')->willReturnSelf();

        $this->orderRepositoryMock->expects($this->once())
            ->method('save')
            ->willThrowException(new \Exception('DB failure'));

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Something went wrong');

        $this->orderStatus->updateStatus($incrementId, $status);
    }
}
