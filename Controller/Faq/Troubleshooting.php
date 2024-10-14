<?php
namespace VaxLtd\ProdfaqsLibrary\Controller\Faq;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

/**
 *
 */
class Troubleshooting extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \VaxLtd\ProdfaqsLibrary\Model\Faqs
     */
    protected $faqsModel;
    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \VaxLtd\ProdfaqsLibrary\Model\Faqs $faqsModel
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \VaxLtd\ProdfaqsLibrary\Model\Faqs $faqsModel,
        \Magento\Framework\Registry $registry
    ) {
        $this->faqsModel = $faqsModel;
        $this->_coreRegistry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        if ($categoryId = $this->getRequest()->getParam('category')) {
            $topic_id = $this->getRequest()->getParam('topic');
            $html = "";

            if ($topic_id) {
                $faq = $this->faqsModel->getFaqsByTopicAndCategory($topic_id, true, ['product_question', 'general_question'], $categoryId);
                $faq_id = 0;
                if ($faq) {
                    foreach ($faq as $f) {
                        $faq_id = $f["id"];
                        $resultPage = $this->resultPageFactory->create();
                        $html .= $resultPage->getLayout()
                            ->createBlock(\VaxLtd\ProdfaqsLibrary\Block\Troubleshooting::class)
                            ->setData('category_id', $categoryId)
                            ->setData('topic_id', $topic_id)
                            ->setFaq($faq_id)
                            ->setTemplate("VaxLtd_ProdfaqsLibrary::question.phtml")->toHtml();
                    }
                }

            }
            $this->getResponse()->setBody($html);
        }

        if ($product_id = $this->getRequest()->getParam('product')) {
            $topic_id = $this->getRequest()->getParam('topic');
            $html = "";
            if ($topic_id) {
                $resultPage = $this->resultPageFactory->create();
                $html = $resultPage->getLayout()
                                   ->createBlock(\VaxLtd\ProdfaqsLibrary\Block\Product\Faqs::class)
                                   ->setData('product_id', $product_id)
                                   ->setData('topic_id', $topic_id)
                                   ->setTemplate("VaxLtd_ProdfaqsLibrary::question.phtml")->toHtml();
            }
            $this->getResponse()->setBody($html);
        }
    }
}
