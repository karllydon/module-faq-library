<?php
namespace VaxLtd\ProdfaqsLibrary\Model\Config\Source;

class Visibility implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'public', 'label' => __('Public')],
            ['value' => 'private', 'label' => __('Private')]
        ];
    }
}
