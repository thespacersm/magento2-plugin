<?php

namespace InPost\Shipment\Controller\Webhook;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Sales\Model\Order\ShipmentRepository;

class Index extends \Magento\Framework\App\Action\Action
    implements \Magento\Framework\App\Action\HttpPostActionInterface,
    CsrfAwareActionInterface
{
    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    private $pageFactory;

    private ShipmentRepository $shipmentRepository;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\ResultFactory $pageFactory,
        ShipmentRepository $shipmentRepository
    ) {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
        $this->shipmentRepository = $shipmentRepository;
    }

    public function createCsrfValidationException(RequestInterface $request): ? InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            throw new NotFoundException(__('Only POST allowed'));
        }

        $json = file_get_contents('php://input');
        $data = json_decode($json,true);

        if ($this->getEvent($data) !== 'shipment_status_changed') {
            return;
        }

        foreach ($data['payload'] as $item) {
//            $this->shipmentRepository->get
        }

        print_r($data);exit;
        $resultRedirect = $this->resultRedirectFactory->create();

        $productIds = $this->getRequest()->getParam('product_ids');

        $result = [
            'success' => true,
            'message' => 'I have been clicked by ajax'
        ];

        $this->getResponse()->representJson(
            $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($result)
        );
    }

    private function getEvent(array $data)
    {
        return $data['event'] ?? null;
    }
}