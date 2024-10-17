<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace VaxLtd\ProdfaqsLibrary\Model\Config\Source;

class visibilityFME implements \Magento\Framework\Option\ArrayInterface
{
   
    public function toOptionArray()
    {
        
        return [['value' => '1', 'label' => __('Yes')],
                ['value' => '0', 'label' => __('No')]];
              //  ['value' => 'registered', 'label' => __('Only Registered')],
              //  ['value' => 'none', 'label' => __('None')]];
    }
}
