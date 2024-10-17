<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace VaxLtd\ProdfaqsLibrary\Model\Config\Source;

class Ratingcustomers implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'all', 'label' => __('All')],
                ['value' => 'guests', 'label' => __('Only Guests')],
                ['value' => 'registered', 'label' => __('Only Registered')],
                ['value' => 'none', 'label' => __('None')]];
    }
}
