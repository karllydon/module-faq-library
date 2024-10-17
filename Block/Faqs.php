<?php

namespace VaxLtd\ProdfaqsLibrary\Block;

use Magento\Framework\View\Element\Template;
use VaxLtd\ProdfaqsLibrary\Helper\Data;
use Magento\Customer\Model\SessionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Store\StoreManager;

class Faqs extends Template
{
    /**
     * @var \VaxLtd\ProdfaqsLibrary\Model\Topic
     */
    protected $topicModel;


    /**
     * @var \VaxLtd\ProdfaqsLibrary\Model\Faqs
     */
    protected $faqsModel;


    /**
     * @var \VaxLtd\ProdfaqsLibrary\Model\Answers
     */
    protected $answersModel;

    /**
     * @var SessionFactory
     */
    protected $sessionFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;


    protected $_coreRegistry = null;
    protected $urlBuilder;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \VaxLtd\ProdfaqsLibrary\Model\Topic $topicModel,
        \VaxLtd\ProdfaqsLibrary\Model\Faqs $faqsModel,
        \VaxLtd\ProdfaqsLibrary\Model\Answers $answersModel,
        SessionFactory $sessionFactory,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        Data $helper
    ) {

        $this->_topicModel = $topicModel;
        $this->_faqsModel = $faqsModel;
        $this->_answersModel = $answersModel;
        $this->sessionFactory = $sessionFactory;
        $this->storeManager = $storeManager;
        $this->_coreRegistry = $registry;
        $this->urlBuilder = $context->getUrlBuilder();
        $this->helper = $helper;
        parent::__construct($context);
    }




    public function getfaqanswers($faq_id)
    {

        $answers = $this->answersModel->loadFaqanswers($faq_id);
        return $answers;
    }

    public function getFrontPageTopics()
    {

        $topicsCollection = $this->topicModel->loadFrontPageTopics();

        return $topicsCollection;
    }


    public function getFrontPageFaqs($topic_id)
    {

        $faqsCollection = $this->faqsModel->loadFaqsOfTopic($topic_id, true);

        return $faqsCollection;
    }

    public function numberOfQuestionToDisplay()
    {
        return $this->helper->getNumOfQuestionsForFaqsPage();
    }

    public function getTopicUrl($topic_id)
    {
        $topic = $this->topicModel->loadTopic($topic_id);
        $topic_identifier = $topic["identifier"];
        $main_identifier = $this->topicModel->getMainPageIdentifer();
        $url_sufix = $this->topicModel->getFaqsSeoIdentifierPostfix();

        $url = $main_identifier . '/' . $topic_identifier . $url_sufix;
        return $this->urlBuilder->getUrl($url);
    }

    public function getMainPageUrl()
    {

        $main_identifier = $this->topicModel->getMainPageIdentifer();
        $url_sufix = $this->topicModel->getFaqsSeoIdentifierPostfix();

        $url = $main_identifier . $url_sufix;
        return $this->urlBuilder->getUrl($url);
    }

    public function getFaqUrl()
    {
        $main_identifier = $this->topicModel->getMainPageIdentifer();
        $url_sufix = $this->topicModel->getFaqsSeoIdentifierPostfix();

        $url = $main_identifier . '/' . $url_sufix;
        return $this->urlBuilder->getUrl($url);
    }

    public function getFaqsBlockNumberOfTopics()
    {

        return $this->helper->getFaqsBlockNumberOfTopics();
    }

    public function isViewMoreLinkEnable()
    {

        return $this->helper->isViewMoreLinkEnable();
    }

    public function isAccordionEnable()
    {

        return $this->helper->isAccordionEnable();
    }

    public function allowedAnswerLength()
    {

        return $this->helper->allowedAnswerLength();
    }

    public function isRatingEnable()
    {

        return $this->helper->isRatingEnable();
    }

    public function isCustomerRatingReadonly()
    {
        $conf = $this->helper->getAllowedCustomerForRating();
        $customer = $this->sessionFactory->create();
        $readonly = 'true';

        switch (true) {
            case $conf == 'all':
                $readonly = 'false';
                break;
            case $conf == 'guests':
                $readonly = $customer->isLoggedIn ? 'true' : 'false';
                break;
            case $conf == 'registered':
                $readonly = $customer->isLoggedIn ? 'false' : 'true';
                break;
            default:
                $readonly = 'true';
        }
        return $readonly;
    }


    public function isCustomerReadonlyStars($faq_id)
    {

        // form configuration
        $conf = $this->isCustomerRatingReadonly();
        if ($conf == 'true') {
            return 1;
        }

        //now check if customer already rated for that faq
        $customerSession = $this->sessionFactory->create();
        $faqRating = $customerSession->getFaqRating();
        $ar = explode(',', $faqRating ?? '');

        $found = array_search($faq_id, $ar);

        return $found;
    }

    public function isCustomerReadonlyLikes($faq_id)
    {

        // form configuration


        //now check if customer already rated for that faq
        $customerSession = $this->sessionFactory->create();
        $faqRating = $customerSession->getLikesRating();
        $ar = explode(',', $faqRating ?? '');

        $found = array_search($faq_id, $ar);

        return $found;
    }
    /*
     * functions for topic detail page
     */

    public function getCurrentTopicDetail()
    {

        $topicData = $this->_coreRegistry->registry('current_topic');
        return $topicData ?: false;
    }

    public function getCurrentFaqDetail()
    {

        $faqData = $this->_coreRegistry->registry('current_faq');
        return $faqData ?: false;
    }

    public function getDetailPageFaqs($topic_id)
    {
        $faqsCollection = $this->faqsModel->loadFaqsOfTopic($topic_id, false);
        return $faqsCollection;
    }

    public function getRatingAjaxUrl()
    {

        $rating_url = $this->urlBuilder->getUrl('prodfaqsLibrary/index/rating');

        return $rating_url;
    }
    public function getLikesAjaxUrl()
    {

        $rating_url = $this->urlBuilder->getUrl('prodfaqsLibrary/index/likes');

        return $rating_url;
    }
    public function getAjaxUrl()
    {

        $rating_url = $this->urlBuilder->getUrl('prodfaqsLibrary/index/faqajax');

        return $rating_url;
    }
    public function getSearchUrl()
    {

        $search_url = $this->urlBuilder->getUrl('prodfaqsLibrary/index/search');

        return $search_url;
    }

    public function getFaqSearchDetail()
    {

        $searchData = $this->_coreRegistry->registry('faq_search_results');

        return $searchData;
    }

    public function getMediaDirectoryUrl()
    {

        $media_dir = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        return $media_dir;
    }
}
