<?php
namespace VaxLtd\ProdfaqsLibrary\Model\Config\Source;

class QuestionType implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'product_question', 'label' => __('Product Question')],
            ['value' => 'general_question', 'label' => __('General Question')],
            ['value' => 'maintenance_item', 'label' => __('Maintenance Question')],
            ['value' => 'extras', 'label' => __('Extras')]
        ];
    }
}
