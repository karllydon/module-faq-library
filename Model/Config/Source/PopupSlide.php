<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace VaxLtd\ProdfaqsLibrary\Model\Config\Source;

class PopupSlide implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'popup', 'label' => __('Popup')],
                ['value' => 'slide', 'label' => __('Slide')]
            ];
    }
}
