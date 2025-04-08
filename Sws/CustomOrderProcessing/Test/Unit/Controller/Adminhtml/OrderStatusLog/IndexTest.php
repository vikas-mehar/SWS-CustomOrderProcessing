<?php
declare(strict_types=1);

namespace Sws\CustomOrderProcessing\Test\Unit\Controller\Adminhtml\OrderStatusLog;

use PHPUnit\Framework\TestCase;
use Sws\CustomOrderProcessing\Controller\Adminhtml\OrderStatusLog\Index;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\View\Page\Title;

class IndexTest extends TestCase
{
    private $contextMock;
    private $pageFactoryMock;
    private $pageMock;
    private $configMock;
    private $titleMock;

    protected function setUp(): void
    {
        $this->contextMock = $this->createMock(Context::class);
        $this->pageFactoryMock = $this->createMock(PageFactory::class);
        $this->pageMock = $this->createMock(Page::class);
        $this->configMock = $this->createMock(PageConfig::class);
        $this->titleMock = $this->createMock(Title::class);
    }

    public function testExecuteReturnsPageResult()
    {
        $this->pageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->pageMock);

        $this->pageMock->expects($this->once())
            ->method('getConfig')
            ->willReturn($this->configMock);

        $this->configMock->expects($this->once())
            ->method('getTitle')
            ->willReturn($this->titleMock);

        $this->titleMock->expects($this->once())
            ->method('prepend')
            ->with(__('Order Status Log'));

        $controller = new Index($this->contextMock, $this->pageFactoryMock);
        $result = $controller->execute();

        $this->assertInstanceOf(Page::class, $result);
    }
}
