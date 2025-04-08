<?php

declare(strict_types=1);

namespace Sws\CustomOrderProcessing\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Sws\CustomOrderProcessing\Model\OrderStatus;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;

class OrderStatusTest extends TestCase
{
    /**
     * @var OrderRepositoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $orderRepositoryMock;

    /**
     * @var OrderStatus
     */
    private $orderStatus;

    protected function setUp(): void
    {
        $this->orderRepositoryMock = $this->createMock(OrderRepositoryInterface::class);
        $this->orderStatus = new OrderStatus($this->orderRepositoryMock);
    }

    public function testUpdateStatusSuccessfully()
    {
        $incrementId = '100000001';
        $status = 'complete';

        // Create a mock order
        $orderMock = $this->createMock(OrderInterface::class);
        $orderMock->method('getId')->willReturn(1);
        $orderMock->method('setStatus')->with($status);

        // Expect the order repository to return the mock order
        $this->orderRepositoryMock->method('get')->with($incrementId)->willReturn($orderMock);
        $this->orderRepositoryMock->expects($this->once())->method('save')->with($orderMock);

        // Call the method
        $result = $this->orderStatus->updateStatus($incrementId, $status);

        // Assert that the returned order is the same as the mock order
        $this->assertSame($orderMock, $result);
    }

    public function testUpdateStatusOrderNotFound()
    {
        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage(__('Order not found.'));

        $incrementId = '100000002';

        // Expect the order repository to throw NoSuchEntityException
        $this->orderRepositoryMock->method('get')->with($incrementId)->willThrowException(new NoSuchEntityException(__('Order not found.')));

        // Call the method
        $this->orderStatus->updateStatus($incrementId, 'complete');
    }

    public function testUpdateStatusGeneralException()
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Something went wrong: Test exception');

        $incrementId = '100000003';

        // Create a mock order
        $orderMock = $this->createMock(OrderInterface::class);
        $orderMock->method('getId')->willReturn(1);

        // Expect the order repository to return the mock order
        $this->orderRepositoryMock->method('get')->with($incrementId)->willReturn($orderMock);
        $this->orderRepositoryMock->method('save')->willThrowException(new \Exception('Test exception'));

        // Call the method
        $this->orderStatus->updateStatus($incrementId, 'complete');
    }
}
