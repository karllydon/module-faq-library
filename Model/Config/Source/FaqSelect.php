<?php

namespace VaxLtd\ProdfaqsLibrary\Model\Config\Source;

class FaqSelect implements \Magento\Framework\Option\ArrayInterface
{
    protected $_faqsModel;
    protected $_coreRegistry;

    /**
     * FaqSelect constructor.
     * @param \VaxLtd\ProdfaqsLibrary\Model\Faqs $faqsModel
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \VaxLtd\ProdfaqsLibrary\Model\Faqs $faqsModel,
        \Magento\Framework\Registry $registry
    )
    {
        $this->_faqsModel = $faqsModel;
        $this->_coreRegistry = $registry;
    }

    /**
     * @return mixed
     */
    public function getFaqOptionArray()
    {
        $options = $this->_faqsModel->getCollection()
            ->addFieldToSelect(['id' => 'faq_id'])
            ->addFieldToSelect(['title' => 'title'])
            ->addFieldToFilter('faq_id', ['neq' => $this->getCurrentFaqId()]);

        return $options;
    }

    public function getCurrentFaqId()
    {
        $faqId = $this->_coreRegistry->registry('faqs')->getId();
        return $faqId ? $faqId : null;
    }

    public function toOptionArray()
    {
        $options = $this->getFaqOptionArray();
        $array = [
            ['value' => '', 'label' => __('Please select a FAQ')]

        ];
        foreach ($options as $option) {
            $array[] = [
                'value' => $option->getId(),
                'label' => __($option->getId() . " - " . $option->getTitle())

            ];
        }
        return $array;
    }
}