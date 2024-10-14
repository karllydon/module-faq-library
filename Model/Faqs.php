<?php

namespace VaxLtd\ProdfaqsLibrary\Model;

use VaxLtd\ProdfaqsLibrary\Helper\Data;
use VaxLtd\ProdfaqsLibrary\Helper\Search;
use Psr\Log\LoggerInterface;

/**
 *
 */
class Faqs
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'prodfaqsLibrary_faqs';

    /**
     * @var \VaxLtd\Prodfaqs\Helper\Data
     */
    protected $helper;

    /**
     * @var Search
     */
    protected $search;


    /**
     * @var LoggerInterface
     */
    protected $logger;


    protected array $faq = [];
    protected array $productsPosition = [];


    /**
     * @param Data $helper
     * @param \Magento\Framework\Model\Context $context
     * @param array $data
     * @param LoggerInterface $logger
     */
    public function __construct(
        Data $helper,
        Search $search,
        LoggerInterface $logger
    ) {
        $this->helper = $helper;
        $this->search = $search;
        $this->logger = $logger;
    }


    /**
     * @return string[]
     */
    public function getAvailableStatuses()
    {
        $availableOptions = [
            '1' => 'Enable',
            '0' => 'Disable'
        ];
        return $availableOptions;
    }

    /**
     * @param $topic_id
     * @param $show_on_main
     * @return array
     */
    public function loadFaqsOfTopic($topic_id, $show_on_main = false)
    {
        $searchParams = ["topic_id" => $topic_id];
        if ($show_on_main) {
            $searchParams["show_on_main"] = 1;
        }
        return $this->search->search($searchParams, "getFaqs");
    }

    /**
     * @param $faq_id
     * @return array|null
     */
    public function loadFaq($faq_id)
    {
        
        
        $searchParams = ["id" => $faq_id];

        $faq =  $this->search->search($searchParams, "getFaqs");


        if ($faq) {
            $this->faq = $faq[0];
            return $faq[0];
        }

        return null;
    }

    /**
     * @param array $object
     * @return mixed
     */
    public function getProducts(array $object)
    {
        return $object["product_id"];
    }


    /**
     * @return array
     */
    public function getCategoriesAttach()
    {
        return $this->faq["categories_name"];
    }

    /**
     * @return array|mixed|null
     */
    public function getProductsPosition()
    {
        if (!$this->faq) {
            return [];
        }
        $array = $this->productsPosition;
        if (!$array) {
            $temp = $this->faq["product_id"];

            for ($i = 0; $i < sizeof($this->faq["product_id"]); $i++) {
                $array[$temp[$i]] = 0;
            }
            $this->productsPosition = $array;
        }
        return $array;
    }

    /**
     * @param $identifier
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkIdentifier($identifier)
    {
        return $this->faq["id"];
    }

    /**
     * @param $prod_id
     * @param bool $publicOnly
     * @param string|null $questionType
     * @return mixed
     */
    public function getFaqsOfProduct($prod_id, $publicOnly = true, $questionType = null)
    {

        $searchParams = ["product_id" => $prod_id];
        if ($publicOnly) {
            $searchParams["public_only"] = 1;
        }
        if ($questionType) {
            $searchParams["question_type"] = $questionType;
        }
        return $this->search->search($searchParams, "getFaqs");
    }

    /**
     * @param $product_id
     * @param $topic_id
     * @param bool $show_on_main
     * @return mixed
     */
    public function loadFaqsOfProductTopic($product_id, $topic_id, $show_on_main = false)
    {
        $searchParams = ["product_id" => $product_id, "topic_id" => $topic_id];
        if ($show_on_main) {
            $searchParams["show_on_main"] = 1;
        }
        return $this->search->search($searchParams, "getFaqs");
    }

    /**
     * @param $categoryId
     * @param $topic_id
     * @param $show_on_main
     * @return mixed
     */
    public function loadFaqsOfCategoryTopic($categoryId, $topic_id, $show_on_main = false)
    {
        $searchParams = ["category_id" => $categoryId, "topic_id" => $topic_id];
        if ($show_on_main) {
            $searchParams["show_on_main"] = 1;
        }
        return $this->search->search($searchParams, "getFaqs");
    }

    /**
     * @param $productId
     * @param bool $publicOnly
     * @param string|null $questionType
     * @return mixed
     */
    public function getTopicsByProduct($productId, $publicOnly = true, $questionType = null)
    {

        $searchParams = ["product_id" => $productId];

        if ($publicOnly) {
            $searchParams["public_only"] = 1;
        }
        if ($questionType) {
            $searchParams["question_type"] = $questionType;
        }
        return $this->search->search($searchParams, "getTopics");
    }

    /**
     * @param $categoryId
     * @param $publicOnly
     * @param $questionType
     * @return array
     */
    public function getTopicsByCategory($categoryId, $publicOnly = true, $questionType = null)
    {
        $searchParams = ["category_id" => $categoryId];

        if ($publicOnly) {
            $searchParams["public_only"] = 1;
        }

        if ($questionType) {
            $searchParams["question_type"] = is_array($questionType)? json_encode($questionType): $questionType;
        }


        return $this->search->search($searchParams, "getTopics");
    }

    /**
     * @param $topicId
     * @param bool $publicOnly
     * @param string|null $questionType
     * @param integer|null $productId
     * @return array
     */
    public function getFaqsByTopic($topicId, $publicOnly = true, $questionType = null, $productId = null)
    {
        $searchParams = ["topic_id" => $topicId];
        if ($publicOnly) {
            $searchParams["public_only"] = 1;
        }

        if ($questionType) {
            $searchParams["question_type"] = $questionType;
        }

        if ($productId) {
            $searchParams["product_id"] = $productId;
        }
        return $this->search->search($searchParams, "getFaqs");
    }

    /**
     * @param $topicId
     * @param $publicOnly
     * @param $questionType
     * @param $categoryId
     * @return array
     */
    public function getFaqsByTopicAndCategory($topicId, $publicOnly = true, $questionType = null, $categoryId = null)
    {
        $searchParams = ["topic_id" => $topicId];
        
        if ($publicOnly) {
            $searchParams["public_only"] = 1;
        }

        if ($questionType) {
            $searchParams["question_type"] = $questionType;
        }

        if ($categoryId) {
            $searchParams["category_id"] = $categoryId;
        }
        return $this->search->search($searchParams, "getFaqs");
    }
}
