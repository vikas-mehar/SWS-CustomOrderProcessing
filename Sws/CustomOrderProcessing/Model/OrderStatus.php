<?php
declare(strict_types=1);

namespace Sws\CustomOrderProcessing\Model;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Sws\CustomOrderProcessing\Api\OrderStatusInterface;
use Magento\Framework\Exception\LocalizedException;

class OrderStatus implements OrderStatusInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    protected OrderRepositoryInterface $orderRepository;

    /**
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository
    )
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param $incrementId
     * @param $status
     * @return OrderInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function updateStatus($incrementId, $status)
    {
        try {
            $order = $this->orderRepository->get($incrementId);
            if (!$order->getId()) {
                throw new NoSuchEntityException(__('Invalid Order ID.'));
            }
            $order->setStatus($status);
            $this->orderRepository->save($order);
            return $order;
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(__('Order not found.'));
        } catch (\Exception $e) {
            throw new LocalizedException(__('Something went wrong: %1', $e->getMessage()));
        }
    }
}
