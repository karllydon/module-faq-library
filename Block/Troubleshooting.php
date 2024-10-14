<?php

namespace VaxLtd\ProdfaqsLibrary\Block;

use Magento\Framework\View\Element\Template;
use Psr\Log\LoggerInterface;


/**
 *
 */
class Troubleshooting extends \VaxLtd\ProdfaqsLibrary\Block\AbstractBlock
{
    /**
     * @var
     */
    protected $_currentStep;
    /**
     * @var
     */
    protected $_currentTopic;
    /**
     * @var
     */
    protected $_currentQuestion;

    /**
     * @var array
     */
    protected $faqArray;



    /**
     * @var \VaxLtd\ProdfaqsLibrary\Model\Faqs
     */
    protected $faqsModel;

    /**
     * @var \VaxLtd\ProdfaqsLibrary\Model\Answers
     */
    protected $answersModel;
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    protected $logger;


    /**
     * @param Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \VaxLtd\ProdfaqsLibrary\Model\Faqs $faqsModel
     * @param \VaxLtd\ProdfaqsLibrary\Model\Answers $answersModel
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \VaxLtd\ProdfaqsLibrary\Model\Faqs $faqsModel,
        \VaxLtd\ProdfaqsLibrary\Model\Answers $answersModel,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        LoggerInterface $logger,
        array $data = [],
    ) {
        $this->faqsModel = $faqsModel;
        parent::__construct($context, $coreRegistry, $faqsModel, $data);
        $this->_filterProvider = $filterProvider;
        $this->answersModel = $answersModel;
        $this->logger = $logger;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->setTemplate('VaxLtd_ProdfaqsLibrary::troubleshooting.phtml');
        $this->_initFaqs();
        parent::_construct();
    }

    /**
     * @return $this
     */
    protected function _initFaqs()
    {
        $this->_currentQuestion = (int)$this->getRequest()->getParam('check', 0);
        $this->_currentTopic = (int)$this->getRequest()->getParam('issue', 0);
        $this->_currentStep = 'issue';
        if ($this->_currentQuestion) {
            $this->_currentStep = 'action';
            $this->_currentTopic = $this->faqsModel->loadFaq($this->_currentQuestion)["topic_id"];


        } elseif ($this->_currentTopic) {
            $this->_currentStep = 'check';
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProgressStep()
    {
        return $this->_currentStep;
    }

    /**
     * @return mixed
     */
    public function getCurrentTopicId()
    {
        return $this->_currentTopic;
    }

    /**
     * @param $topic_id
     * @return void
     */
    public function setCurrentTopicId($topic_id)
    {
        $this->_currentTopic = $topic_id;
    }

    /**
     * @return mixed
     */
    public function getFaq()
    {
        return $this->faqArray;
    }

    /**
     * @return mixed
     */
    public function getCurrentQuestionId()
    {
        return $this->_currentQuestion;
    }

    /**
     * @param $publicOnly
     * @param $questionType
     * @return array
     */
    public function getQuestions($publicOnly = true, $questionType = null)
    {
        $product_id = null;
        if ($this->getProduct() != null) {
            $product_id = $this->getProduct()->getId();
        }
        
        $questions = $this->faqsModel->getFaqsByTopic($this->getCurrentTopicId(), $publicOnly, $questionType, $product_id);

        return $questions;
    }

    /**
     * @param $publicOnly
     * @param $questionType
     * @return array|null
     */
    public function getQuestionsByCategory($publicOnly = true, $questionType = null)
    {
        $categoryId = $this->getData('category_id');
        $curentCategory = $this->getCurrentCategory();

        if ($curentCategory != null) {
            $categoryId = $curentCategory->getId();
        }


        $questions = $this->faqsModel->getFaqsByTopicAndCategory($this->getCurrentTopicId(), $publicOnly, $questionType, $categoryId);
        
        return $questions;
    }

    /**
     * @param $publicOnly
     * @param $questionType
     * @param $question_id
     * @return mixed
     */
    public function getFaqAnswer($publicOnly, $questionType, $question_id = null)
    {
        $question_id = $question_id ?: $this->getCurrentQuestionId();

        $questionCollection = $this->getQuestionsByCategory($publicOnly, $questionType);

        $filter = function($question) use ($question_id) {
            return $question["id"] == $question_id;
        };

        $faqAnswer = array_filter($questionCollection, $filter)[0];



        return $faqAnswer;
    }


    /**
     * @param $faqId
     * @return string
     * @throws \Exception
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
     * @param $question_id
     * @return $this
     */
    public function setFaq($question_id)
    {

        $this->_currentQuestion = $question_id;
        $this->_currentTopic = $this->faqsModel->loadFaq($this->_currentQuestion)["topic_id"];


        $faq = $this->getFaqAnswer($this->getPublicOnly(), $this->getQuestionTypes(), $question_id);
        $this->faqArray = $faq;

        return $this;
    }

    /**
     * @param $question_id
     * @return string
     */
    public function getFaqHtml($question_id)
    {
        $this->setFaq($question_id);
        return $this->setTemplate("VaxLtd_Prodfaqs::question.phtml")->toHtml();
    }

    /**
     * @return mixed|null
     */
    public function getProductCollection()
    {
        return $this->coreRegistry->registry('loaded_product_collection');
    }

    /**
     * @return mixed|null
     */
    public function getCurrentCategory()
    {
        return $this->coreRegistry->registry('current_category');
    }

    /**
     * @param bool $publicOnly
     * @param string | null $questionType
     * @return array
     */
    public function getTopicsCollection($publicOnly = true, $questionType = null)
    {
        $topics = [];
        $productCollection = $this->getProductCollection();

        foreach ($productCollection as $product) {
            $topicsProduct = $this->faqsModel->getTopicsByProduct($product->getEntityId(), $publicOnly, $questionType);
            foreach ($topicsProduct as $topicProduct) {
                if (!array_key_exists($topicProduct['topic_id'], $topics)) {
                    $topics[$topicProduct['topic_id']] = $topicProduct;
                }
            }
        }

        return array_values($topics);
    }

    /**
     * @param $publicOnly
     * @param $questionType
     * @return array
     */
    public function getTopicsCollectionByCategory($publicOnly = true, $questionType = null)
    {

        $topics = [];
        $currentCategoryId = $this->getCurrentCategory()->getEntityId();
        
        $topicsProduct = $this->faqsModel->getTopicsByCategory($currentCategoryId, $publicOnly, $questionType);


        foreach ($topicsProduct as $topicProduct) {
            //topicsProduct has array of category_ids rather than one searched, have to remove this and use the one searched
            $topicProduct["category_id"] = $currentCategoryId;

            if (!array_key_exists($topicProduct['topic_id'], $topics)) {
                $topics[$topicProduct['topic_id']] = $topicProduct;
            }
        }

        return array_values($topics);
    }
}
