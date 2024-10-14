<?php

namespace VaxLtd\ProdfaqsLibrary\Controller\Test;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Request\Http;

use VaxLtd\ProdfaqsLibrary\Model\Answers;
use Psr\Log\LoggerInterface;

class Index extends \Magento\Framework\App\Action\Action
{
    
    protected $jsonFactory;
        
    protected $request;

    protected $logger;

    protected $answers;
        
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        Http $request,
        LoggerInterface $logger,
        Answers $answers
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->request = $request;
        $this->logger = $logger;
        $this->answers = $answers;
        parent::__construct($context);
    }



    public function execute() {
        $resultJson = $this->jsonFactory->create();


        $data = $this->answers->loadFaqanswers(48);
      
        return $resultJson->setData($data);
    }


}