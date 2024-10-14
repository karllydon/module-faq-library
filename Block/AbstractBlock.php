<?php
namespace VaxLtd\ProdfaqsLibrary\Block;

use Magento\Framework\View\Element\Template;
use VaxLtd\ProdfaqsLibrary\Model\Faqs;

abstract class AbstractBlock extends Template
{
    /**
     * @var Faqs;
     */
    protected $faqs;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     *  constructor.
     * @param Template\Context $context
     * @param array $data
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \VaxLtd\Prodfaqs\Model\FaqsFactory $faqsFactory
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        Faqs $faqs,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->coreRegistry = $coreRegistry;
        $this->faqs = $faqs;
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        if ($product = $this->coreRegistry->registry('current_product')) {
            return $product;
        }
    }

    /**
     * @param bool $publicOnly
     * @param string|null $questionType
     * @return mixed
     */
    public function getTopics($publicOnly = true, $questionType = null)
    {
        return $this->faqs->getTopicsByProduct($this->getProduct()->getId(), $publicOnly, $questionType);
    }

    /**
     * @param bool $publicOnly
     * @param string|null $questionType
     * @return mixed
     */
    public function getProductRelatedFaqs($publicOnly = true, $questionType = null)
    {
        $product_id = $this->getProduct()->getId();
        $product_faqs = $this->faqs->getFaqsOfProduct($product_id, $publicOnly, $questionType);
        return $product_faqs;
    }
}