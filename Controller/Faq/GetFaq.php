<?php
namespace VaxLtd\ProdfaqsLibrary\Controller\Faq;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Psr\Log\LoggerInterface;

class GetFaq extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;

    protected $logger;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        LoggerInterface $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->logger = $logger;
        parent::__construct($context);
    }

    public function execute()
    {
        $faq_id = $this->getRequest()->getParam('faq_id');
        $resultPage = $this->resultPageFactory->create();
        $html = $resultPage->getLayout()
            ->createBlock(\VaxLtd\ProdfaqsLibrary\Block\Troubleshooting::class)
            ->setFaq($faq_id)
            ->setTemplate("VaxLtd_ProdfaqsLibrary::question.phtml")->toHtml();

        $this->getResponse()->setBody($html);
    }
}
