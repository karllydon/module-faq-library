<?php

namespace VaxLtd\ProdfaqsLibrary\Model;


use VaxLtd\ProdfaqsLibrary\Helper\Data;
use VaxLtd\ProdfaqsLibrary\Helper\Search;


class Answers
{
    /**
     * Statuses
     */ 
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;


    const CACHE_TAG = 'prodfaqs_answer';

    /**
     * @var string
     */
    protected $_cacheTag = 'prodfaqs_answer';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'prodfaqs_answer';

    protected $helper;

    protected $search;


    public function __construct(
        Data $helper,
        Search $search,
    ) {
        $this->helper = $helper;
        $this->search = $search;
    }


    public function loadAnswersCount($faq_id)
    {
        $searchParams = ["id" => $faq_id];
        return $this->search->search($searchParams,  "getAnswers");
    }

    public function loadFaqanswers($faq_id)
    {
        $searchParams = ["id" => $faq_id, "answer_status" => 1];
        return $this->search->search($searchParams,  "getAnswers");
    }
  
    /**
     * Prepare item's statuses
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }
}
