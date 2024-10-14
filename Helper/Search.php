<?php

namespace VaxLtd\ProdfaqsLibrary\Helper;

use Magento\Framework\App\Helper\Context;
use Psr\Log\LoggerInterface;
use  \Magento\Framework\Serialize\Serializer\Json;

class Search extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $logger;

    /**
     * @var Json
     */
    protected $jsonHelper;


    /**
     * FaqsSearch constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context,
        Json $jsonHelper,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->jsonHelper = $jsonHelper;
        $this->logger = $logger;
    }

    /**
     * 
     * search with given query params
     * @param array searchParams
     * @param string searchEndpoint
     * @return array
     */
    public function search($searchParams, $searchEndpoint)
    {

        $url = 'https://library.vax.com/api/faqs/' . $searchEndpoint . '?' . http_build_query($searchParams);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->logger->debug("libraryv2/faqsearch/". $searchEndpoint . print_r(
            [
                "url" => $url,
                "http_status" => $http_status,
                "response" => $data
            ],
            true
        ));

        if ($http_status != 200) {
            return [];
        }

        return $this->jsonHelper->unserialize($data);
    }
}
