<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace VaxLtd\ProdfaqsLibrary\Model\Config\Source;

class Sortby implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'latest', 'label' => __('Latest')],
                ['value' => 'asc', 'label' => __('Ascending Order')],
                ['value' => 'desc', 'label' => __('Descending Order')]];
    }
}
