<?php

namespace VaxLtd\ProdfaqsLibrary\Block\Product;

use Magento\Cms\Model\Template\FilterProvider;
use VaxLtd\ProdfaqsLibrary\Helper\Data;
use VaxLtd\ProdfaqsLibrary\Helper\Search;
use Psr\Log\LoggerInterface;


class Faqs extends \VaxLtd\ProdfaqsLibrary\Block\Faqs
{
    const CONFIG_CAPTCHA_ENABLE = 'prodfaqslibrary/google_options/captchastatus';
    const CONFIG_CAPTCHA_PRIVATE_KEY = 'prodfaqslibrary/google_options/googleprivatekey';
    const CONFIG_CAPTCHA_PUBLIC_KEY = 'prodfaqslibrary/google_options/googlepublickey';
    const CONFIG_CAPTCHA_THEME = 'prodfaqslibrary/google_options/theme';

    const PFAQS_HEADING = 'prodfaqslibrary/product_questions/title';
    const PFAQS_ASK_ENABLE = 'prodfaqslibrary/product_questions/enable_ask';
    const PFAQS_CUSTOMR_ASK_ALLOWED = 'prodfaqslibrary/product_questions/allow_customers';
    const PFAQS_ASK_POPUPSLIDE   = 'prodfaqslibrary/product_questions/open_form';

    const PFAQS_FAQS_SORTBY   = 'prodfaqslibrary/product_questions/sortby';


    const ANS_ASK_ENABLE = 'prodfaqslibrary/answers/enable_ask';
    const ANS_CUSTOMR_ASK_ALLOWED = 'prodfaqslibrary/answers/allow_customers';

    const ANS_LIKES_ENABLE = 'prodfaqslibrary/answers/enable_likes';
    const ANS_LIKES_ALLOWED = 'prodfaqslibrary/answers/likes_allow_customers';
    const PFAQS_FAQS_LOADER = 'prodfaqslibrary/ajaxloader/placeholder';
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;
    /**
     * @var \VaxLtd\ProdfaqsLibrary\Model\Answers
     */
    protected $answersModel;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $_storeManager;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Model\SessionFactory $sessionFactory,
     */
    protected $sessionFactory;


    /**
     * @var FilterProvider
     */
    protected $filterProvider;

    /** 
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var Search
     */
    protected $search;


    /**
     * @var \VaxLtd\ProdfaqsLibrary\Model\Faqs
     */
    protected $faqsModel;


    /**
     * @var LoggerInterface
     */
    protected $logger;



    /**
     * @param FilterProvider $filterProvider
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \VaxLtd\ProdfaqsLibrary\Model\Topic $topicModel
     * @param \VaxLtd\ProdfaqsLibrary\Model\Faqs $faqsModel
     * @param \VaxLtd\ProdfaqsLibrary\Model\Answers $answersModel
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Registry $registry
     * @param Search $search 
     */
    public function __construct(
        FilterProvider $filterProvider,
        \Magento\Framework\View\Element\Template\Context $context,
        \VaxLtd\ProdfaqsLibrary\Model\Topic $topicModel,
        \VaxLtd\ProdfaqsLibrary\Model\Faqs $faqsModel,
        \VaxLtd\ProdfaqsLibrary\Model\Answers $answersModel,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        \Magento\Framework\Registry $registry,
        Data $helper,
        Search $search,
        LoggerInterface $logger
    ) {
        $this->filterProvider = $filterProvider;
        $this->urlBuilder = $context->getUrlBuilder();
        $this->scopeConfig = $context->getScopeConfig();
        $this->customerSession = $customerSession;
        $this->sessionFactory = $sessionFactory;
        $this->_storeManager = $context->getStoreManager();
        $this->faqsModel = $faqsModel;
        $this->answersModel = $answersModel;
        $this->search = $search;
        $this->logger = $logger;
        parent::__construct($context, $topicModel, $faqsModel, $answersModel, $sessionFactory, $context->getStoreManager(), $registry, $helper);
        $this->setTabTitle();
    }


    /**
     * @param $product_id
     * @param $question_type
     * @return array
     */
    public function getProductPageFaqsByQuestionType($product_id, $question_type)
    {

        $searchParams = ["product_id" => $product_id, "question_type" => $question_type, "status" => 1];
        $faqs = $this->search->search($searchParams, "getFaqs");

        if ($this->getSortbyOrder() == 'asc') {
            usort($faqs, function ($item1, $item2) {
                return $item1['faq_order'] <=> $item2['faq_order'];
            });
        } else {
            usort($faqs, function ($item1, $item2) {
                return $item2['faq_order'] <=> $item1['faq_order'];
            });
        }

        return $faqs;
    }

    /**
     * @param $product_id
     * @param $question_type
     * @return array
     */
    public function getProductTopicByQuestionType($product_id, $question_type)
    {

        $faqs = $this->getProductPageFaqsByQuestionType($product_id, $question_type);
        $topics = [];


        if (count($faqs) > 0) {
            $topic_ids = [];
            foreach ($faqs as $faq) {
                $topic_ids[] = $faq["topic_id"];
            }
            $topic_ids = array_unique($topic_ids);

            foreach ($topic_ids as $id) {
                $topic = $this->topicModel->loadTopic($id);
                $topics[$id] = $topic["topic_title"];
            }
        }
        return $topics;
    }

    /**
     * @param $product_id
     * @param $topic_id
     * @param $show_on_main
     * @return mixed
     */
    public function getFaqsOfProductTopic($product_id, $topic_id, $show_on_main)
    {

        return $this->faqsModel->loadFaqsOfProductTopic($product_id, $topic_id, $show_on_main);
    }

    /**
     * @return mixed|null
     */
    public function getProductCollection()
    {

        return $this->_coreRegistry->registry('loaded_product_collection');
    }

    /**
     * @param $question_type
     * @return array
     */
    public function getProductsTopic($question_type)
    {

        $topics = [];
        $products = $this->getProductCollection();
        foreach ($products as $product) {
            $productTopics = $this->getProductTopicByQuestionType($product->getEntityId(), $question_type);
            if ($productTopics) {
                $topicsDiff = array_diff($productTopics, $topics);
                $topics += $topicsDiff;
            }
        }
        return $topics;
    }

    /**
     * @param $question_type
     * @return array
     */
    public function getCategoryTopic($question_type)
    {

        $topics = [];
        $currentCategory = $this->_coreRegistry->registry('current_category');


        if ($currentCategory) {
            $topics = $this->getCategoryTopicByQuestionType($currentCategory->getId(), $question_type);
        }


        return $topics;
    }

    /**
     * @param $categoryId
     * @param $question_type
     * @return array
     */
    public function getCategoryTopicByQuestionType($categoryId, $question_type)
    {
        $question_type = is_array($question_type)? json_encode($question_type): $question_type;

        $searchParams = ["category_id" => $categoryId, "question_type" => $question_type, "status" => 1, "topic_status" => 1];
        $topics = $this->search->search($searchParams, "getTopics");

        if (count($topics) == 0) {
            return [];
        }

        if ($this->getSortbyOrder() == 'asc') {
            usort($topics, function ($item1, $item2) {
                return $item1['faq_order'] <=> $item2['faq_order'];
            });
        } else {
            usort($topics, function ($item1, $item2) {
                return $item2['faq_order'] <=> $item1['faq_order'];
            });
        }

        return $topics;
    }

    /**
     * @param $topic_id
     * @return array
     */
    public function getFaqsCollection($topic_id)
    {

        $faqs = [];
        $products = $this->getProductCollection();
        foreach ($products as $product) {
            $faqsOfProduct = $this->getFaqsOfProductTopic($product->getEntityId(), $topic_id, false);
            foreach ($faqsOfProduct as $faq) {
                if (!array_key_exists($faq["id"], $faqs)) {
                    $faqs[$faq["id"]] = $faq;
                }
            }
        }
        return $faqs;
    }

    /**
     * @param $topic_id
     * @return array
     */
    public function getFaqsCollectionByTopicId($topic_id)
    {

        $currentCategory = $this->_coreRegistry->registry('current_category');
        return $this->faqsModel->loadFaqsOfCategoryTopic($currentCategory->getId(), $topic_id, false);
    }

    /**
     * @param array $faq
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAnswerContentByFaq($faq)
    {
        $searchParams = ["id" => $faq["id"], "status" => 1];
        $answers = $this->search->search($searchParams, "getAnswers")[0];

        if ($answers && $answers["id"]) {
            return $this->filterProvider->getBlockFilter()->filter($answers["answer_answer"]);
        }
        return $faq["faq_answer"];
    }

    /*
     * Gets one answer if given a faqId
     */
    public function getNewFaqAnswer($faqId)
    {

        $answers = $this->answersModel->loadFaqanswers($faqId);
        /* return only the first answer as we can only handle one answer per question template*/
        foreach ($answers as $answer) {

            /* return HTML content from a WYSIWYG */
            return $this->_filterProvider->getBlockFilter()->filter($answer["answer_answer"]);
        }

        /*return blank incase of answer not found*/
        return "";
    }


    /**
     * @return mixed
     */
    public function getLoginId()
    {
        $customer = $this->sessionFactory->create();
        return ($customer->getCustomer()->getId());
    }

    /**
     * @return mixed
     */
    public function getLoginEmail()
    {
        $customer = $this->sessionFactory->create();
        return ($customer->getCustomer()->getEmail());
    }

    /**
     * @return mixed
     */
    public function getLoginName()
    {
        $customer = $this->sessionFactory->create();
        return $customer->getCustomer()->getName();
    }

    /**
     * Get current product id
     *
     * @return null|int
     */
    public function getProductId()
    {
        $product = $this->_coreRegistry->registry('product');
        return $product?->getId();
    }

    /**
     * @return null
     */
    public function getProductName()
    {
        $product = $this->_coreRegistry->registry('product');
        return $product?->getName();
    }

    /**
     * @return null
     */
    public function getProductUrl()
    {
        $product = $this->_coreRegistry->registry('product');
        return $product?->getUrlModel()?->getUrl($product);
    }

    /**
     * Set tab title
     *
     * @return void
     */
    public function setTabTitle()
    {
        $title = __('Faqs');
        $this->setTitle($title);
    }

    /**
     * @return string
     */
    public function getFaqPostUrl()
    {
        return $this->urlBuilder->getUrl('prodfaqslib/index/post');
    }

    /**
     * @return string
     */
    public function getAnswerPostUrl()
    {
        return $this->urlBuilder->getUrl('prodfaqslib/index/answerpost');
    }

    /**
     * @param $product_id
     * @return mixed
     */
    public function getProductPageFaqs($product_id)
    {


        $searchParams = ["product_id" => $product_id, "status" => 1];
        $faqs = $this->search->search($searchParams, "getFaqs");

        if ($this->getSortbyOrder() == 'asc') {
            usort($faqs, function ($item1, $item2) {
                return $item1['faq_order'] <=> $item2['faq_order'];
            });
        } else {
            usort($faqs, function ($item1, $item2) {
                return $item2['faq_order'] <=> $item1['faq_order'];
            });
        }


        return $faqs;
    }

    /**
     * @param $product_id
     * @param $type
     * @param $arrow
     * @return mixed
     */
    public function getProductPageAjaxFaqs($product_id, $type, $arrow)
    {

        $searchParams = ["product_id" => $product_id, "status" => 1];
        $faqs = $this->search->search($searchParams, "getFaqs");

        switch (true){
            case ($type == 0 && $arrow == 'asc'):
                usort($faqs, function ($item1, $item2) {
                    return $item1['rating_Stars'] <=> $item2['rating_stars'];
                });
                break;
            case ($type == 0 && $arrow == 'desc'):
                usort($faqs, function ($item1, $item2) {
                    return $item2['rating_Stars'] <=> $item1['rating_stars'];
                });
                break;
            
        }







        // if ($type == 0 && $arrow == 'asc') {
        //     $collection->setOrder('rating_stars', 'asc');
        // } elseif ($type == 0 && $arrow == 'desc') {
        //     $collection->setOrder('rating_stars', 'desc');
        // } elseif ($type == 1 && $arrow == 'asc') {
        //     $collection->setOrder('create_date', 'asc');
        // } elseif ($type == 1 && $arrow == 'desc') {
        //     $collection->setOrder('create_date', 'desc');
        // }

        return $faqs;
    }

    /**
     * @param $faq_id
     * @return array|null
     */
    public function getfaqanswers($faq_id)
    {
        $answers = $this->answersModel->loadFaqanswers($faq_id);
        return $answers;
    }

    /**
     * @param $moduleName
     * @param $type
     * @return string
     */
    public function df_module_dir($moduleName, $type = '')
    {
        /** @var \Magento\Framework\ObjectManagerInterface $om */
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\Module\Dir\Reader $reader */
        $reader = $om->get('Magento\Framework\Module\Dir\Reader');
        return $reader->getModuleDir($type, $moduleName);
    }

    /**
     * @return mixed
     */
    public function isCaptchaEnable()
    {
        return $this->scopeConfig->getValue(self::CONFIG_CAPTCHA_ENABLE);
    }

    /**
     * @return mixed
     */
    public function getPrivateKey()
    {
        return $this->scopeConfig->getValue(self::CONFIG_CAPTCHA_PRIVATE_KEY);
    }

    /**
     * @return mixed
     */
    public function getPublicKey()
    {
        return $this->scopeConfig->getValue(self::CONFIG_CAPTCHA_PUBLIC_KEY);
    }

    /**
     * @return mixed
     */
    public function getCaptchaTheme()
    {
        return $this->scopeConfig->getValue(self::CONFIG_CAPTCHA_THEME);
    }

    /**
     * @return \Magento\Framework\Phrase|mixed
     */
    public function getProductFaqsHeading()
    {
        $title = $this->scopeConfig->getValue(self::PFAQS_HEADING);
        return $title ? $title : __('Faqs');
    }

    /**
     * @return mixed
     */
    public function getSortbyOrder()
    {
        $order = $this->scopeConfig->getValue(self::PFAQS_FAQS_SORTBY);
        return $order;
    }

    /**
     * @return mixed
     */
    public function getAjaxLoader()
    {
        $loader = $this->scopeConfig->getValue(self::PFAQS_FAQS_LOADER);
        return $loader;
    }

    /**
     * @return mixed
     */
    public function isAskQuestionEnable()
    {
        return $this->scopeConfig->getValue(self::PFAQS_ASK_ENABLE);
    }

    /**
     * @return bool
     */
    public function isAskQuestionAllowed()
    {
        $conf = $this->scopeConfig->getValue(self::PFAQS_CUSTOMR_ASK_ALLOWED);
        $customer = $this->sessionFactory->create();

        $allow = false;
        if ($conf == 'all') {
            $allow = true;
        } elseif ($conf == 'guests') {
            if (!$customer->isLoggedIn()) {
                $allow = true;
            } else {
                $allow = false;
            }
        } elseif ($conf == 'registered') {
            if ($customer->isLoggedIn()) {
                $allow = true;
            } else {
                $allow = false;
            }
        } else {
            $allow = false;
        }
        return $allow;
    }
    ////////////////////////  ANSWER Settings

    /**
     * @return mixed
     */
    public function isAskAnswerEnable()
    {
        return $this->scopeConfig->getValue(self::ANS_ASK_ENABLE);
    }

    /**
     * @return bool
     */
    public function isAskAnswerAllowed()
    {
        $conf = $this->scopeConfig->getValue(self::ANS_CUSTOMR_ASK_ALLOWED);
        $customer = $this->sessionFactory->create();

        $allow = false;
        if ($conf == 'all') {
            $allow = true;
        } elseif ($conf == 'guests') {
            if (!$customer->isLoggedIn()) {
                $allow = true;
            } else {
                $allow = false;
            }
        } elseif ($conf == 'registered') {
            if ($customer->isLoggedIn()) {
                $allow = true;
            } else {
                $allow = false;
            }
        } else {
            $allow = false;
        }
        return $allow;
    }

    /**
     * @return mixed
     */
    public function isLikesEnable()
    {
        return $this->scopeConfig->getValue(self::ANS_LIKES_ENABLE);
    }

    /**
     * @return bool
     */
    public function isLikesAllowed()
    {
        $conf = $this->scopeConfig->getValue(self::ANS_LIKES_ALLOWED);
        $customer = $this->sessionFactory->create();

        $allow = false;
        if ($conf == 'all') {
            $allow = true;
        } elseif ($conf == 'guests') {
            if (!$customer->isLoggedIn()) {
                $allow = true;
            } else {
                $allow = false;
            }
        } elseif ($conf == 'registered') {
            if ($customer->isLoggedIn()) {
                $allow = true;
            } else {
                $allow = false;
            }
        } else {
            $allow = false;
        }
        return $allow;
    }

    /**
     * @return mixed|string
     */
    public function openFormPopupSlide()
    {
        $popupSlide = $this->scopeConfig->getValue(self::PFAQS_ASK_POPUPSLIDE);
        return $popupSlide ? $popupSlide : 'popup';
    }
}
