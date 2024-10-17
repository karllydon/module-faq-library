<?php

namespace VaxLtd\ProdfaqsLibrary\Model;

use VaxLtd\ProdfaqsLibrary\Helper\Search;
use VaxLtd\ProdfaqsLibrary\Helper\Data;

class Topic
{

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'prodfaqs_topic';


    protected $storeManager;

    /**
     * 
     * @var Search
     */
    protected $search;

    /**
     * @var Data
     */
    protected $helper;


    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Search $search,
        Data $helper
    ) {

        $this->storeManager = $storeManager;
        $this->search = $search;
        $this->helper = $helper;
    }

    public function getAvailableStatuses()
    {


        $availableOptions = [
            '1' => 'Enable',
            '0' => 'Disable'
        ];

        return $availableOptions;
    }

    /**
     * 
     * @param integer $topic_id
     * @return array|null
     */
    public function loadTopic($topic_id){
        $searchParams = ["topic_id" => $topic_id, "topic_status" => 1];
        $topics = $this->search->search($searchParams, "getTopics");
        return $topics[0];
    }

    /*
         * topic list for admin dropdown, to attach with faqs
         */

    public function getTopicsList()
    {
        $searchParams = ["topic_status" => 1];
        $topics = $this->search->search($searchParams, "getTopics");
        $topicList = [];
        foreach ($topics as $data) {
            $topicList[$data["topic_id"]] = $data["topic_title"];
        }
        return $topicList;
    }

    public function gettopics()
    {

        $searchParams = ["topic_status" => 1];
        $topics = $this->search->search($searchParams, "getTopics");
        $topicList = [];

        $i = 0;
        foreach ($topics as $data) {
            $topicList[$i] = ['value' => $data["topic_id"], 'label' => __($data["topic_title"])];
            $i++;
        }


        return $topicList;
    }


    public function loadFrontPageTopics()
    {
        $searchParams = ["topic_status" => 1];
        if ($this->helper->displaySelectedTopics()) {
            $searchParams["show_on_main"] = 1;
        }

        $topics = $this->search->search($searchParams, "getTopics");
        usort($topics, function ($item1, $item2) {
            return $item1['topic_order'] <=> $item2['topic_order'];
        });

        return $topics;
    }


    public function getMainPageIdentifer()
    {
        return $this->helper->getFaqsPageIdentifier();
    }

    public function getFaqsSeoIdentifierPostfix()
    {
        return $this->helper->getFaqsSeoIdentifierPostfix();
    }
}
