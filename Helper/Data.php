<?php

namespace VaxLtd\ProdfaqsLibrary\Helper;

use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PRODUCT_FAQ_SORTING =    'prodfaqslibrary/product_questions/sort_by';

    const XML_FAQS_ENABLE               = 'prodfaqslibrary/list/enabled';
    const XML_LIBRARY_URL            = 'prodfaqslibrary/list/library_url';
    const XML_LIBRARY_KEY            = 'prodfaqslibrary/list/library_api_key';




    const XML_FAQS_PAGE_TITLE           = 'prodfaqslibrary/list/page_title';
    const XML_FAQS_IDENTIFIER           = 'prodfaqslibrary/list/identifier';
    const XML_FAQS_META_KEYWORDS        = 'prodfaqslibrary/list/meta_keywords';
    const XML_FAQS_META_DESC            = 'prodfaqslibrary/list/meta_description';
    const XML_FAQS_DISPLAY_TOPICS       = 'prodfaqslibrary/list/display_topics';
    const XML_FAQS_NUM_OF_QUESTIONS     = 'prodfaqslibrary/list/show_number_of_questions';
    const XML_FAQS_VIEW_MORE            = 'prodfaqslibrary/list/enable_view_more';
    const XML_FAQS_ACCORDION            = 'prodfaqslibrary/list/enable_accordion';
    const XML_ANSWER_LENGTH             = 'prodfaqslibrary/list/answer_length';

    const XML_RATING_ENABLE             = 'prodfaqslibrary/rating/enable';
    const XML_FAQS_ALLOW_CUSTOMERS      = 'prodfaqslibrary/rating/allow_customers';

    const XML_FAQS_BLOCK                = 'prodfaqslibrary/general/faq_block';
    const XML_FAQS_SEARCH_BLOCK         = 'prodfaqslibrary/general/faq_search_block';
    const XML_FAQS_MAX_TOPIC            = 'prodfaqslibrary/general/faq_maxtopic';
    const XML_TAGS_BLOCK                = 'prodfaqslibrary/general/tags_block';
    const XML_MAX_TAGS                  = 'prodfaqslibrary/general/max_tags';

    const XML_FAQS_URL_SUFFIX           = 'prodfaqslibrary/seo/url_suffix';
    const TIME_ZONE = 'general/locale/timezone';


    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_requestHttp;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;


    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;


    protected $_urlBuilder;

    protected $_backendHelper;

    /**
     * Data constructor.
     * @param Context $context
     * @param \Magento\Framework\App\RequestInterface $requestHttp
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\RequestInterface $requestHttp,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\Helper\Data $backendHelper
    ) {
        $this->_storeManager = $storeManager;
        $this->_urlBuilder = $context->getUrlBuilder();
        $this->_backendHelper = $backendHelper;
        parent::__construct($context);
        $this->_requestHttp = $requestHttp;
        $this->scopeConfig = $scopeConfig;
    }


    public function isModuleEnabled()
    {
        
        if ($this->isModuleOutputEnabled('VaxLtd_ProdfaqsLibrary') &&
                $this->scopeConfig->isSetFlag(
                    self::XML_FAQS_ENABLE,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )) {
            return true;
        }
    }
       
    public function getLibraryUrl()
    {
        return $this->scopeConfig->getValue(
            self::XML_LIBRARY_URL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getLibraryKey()
    {
        return $this->scopeConfig->getValue(
            self::XML_LIBRARY_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function timezone()
    {
        return $this->scopeConfig->getValue(
            self::TIME_ZONE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getFaqsPageTitle()
    {
        
        return $this->scopeConfig->getValue(
            self::XML_FAQS_PAGE_TITLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    
    public function getFaqsPageIdentifier()
    {
        
        return $this->scopeConfig->getValue(
            self::XML_FAQS_IDENTIFIER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getFaqsPageMetaKeywords()
    {
        
        return $this->scopeConfig->getValue(
            self::XML_FAQS_META_KEYWORDS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function getFaqsPageMetaDesc()
    {
        
        return $this->scopeConfig->getValue(
            self::XML_FAQS_META_DESC,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function displaySelectedTopics()
    {
        
        return $this->scopeConfig->getValue(
            self::XML_FAQS_DISPLAY_TOPICS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function getNumOfQuestionsForFaqsPage()
    {
        
        return $this->scopeConfig->getValue(
            self::XML_FAQS_NUM_OF_QUESTIONS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function isViewMoreLinkEnable()
    {
        
        return $this->scopeConfig->getValue(
            self::XML_FAQS_VIEW_MORE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function isAccordionEnable()
    {
        
        return $this->scopeConfig->getValue(
            self::XML_FAQS_ACCORDION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function allowedAnswerLength()
    {
        
        $length = $this->scopeConfig->getValue(
            self::XML_ANSWER_LENGTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        
        return ($length == '') ? 0 : $length;
    }
    
    public function isRatingEnable()
    {
        
        return $this->scopeConfig->getValue(
            self::XML_RATING_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function getAllowedCustomerForRating()
    {
        
        return $this->scopeConfig->getValue(
            self::XML_FAQS_ALLOW_CUSTOMERS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function isFaqsBlockEnable()
    {
        
        return $this->scopeConfig->getValue(
            self::XML_FAQS_BLOCK,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function isFaqsSearchBlockEnable()
    {
        
        return $this->scopeConfig->getValue(
            self::XML_FAQS_SEARCH_BLOCK,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function getFaqsBlockNumberOfTopics()
    {
        
        return $this->scopeConfig->getValue(
            self::XML_FAQS_MAX_TOPIC,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function getTagsMaxNum()
    {
        
        return $this->scopeConfig->getValue(
            self::XML_MAX_TAGS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function getFaqsSeoIdentifierPostfix()
    {
        
        return $this->scopeConfig->getValue(
            self::XML_FAQS_URL_SUFFIX,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    
    public function getListingMainPageUrl()
    {
        
        $main_identifier = $this->getFaqsPageIdentifier();
        $url_sufix = $this->getFaqsSeoIdentifierPostfix();
        
        if($main_identifier != ''){
            $url = $main_identifier.$url_sufix;
            return $this->_urlBuilder->getUrl($url);
        }
        
        return $this->_urlBuilder->getUrl('prodfaqslibrary');
    }
    public function getProductsGridUrl()
    {
        
        return  $this->_backendHelper->getUrl('prodfaqslibrary/faqs/products', ['_current' => true]);
    }
    


    /**
     * @return array
     */
    protected function _getPrivateIps()
    {
        $config_ip_addresses = $this->scopeConfig->getValue('prodfaqslibrary/access_control/ip_addresses', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $textIp = str_replace(' ', ',', $config_ip_addresses ?? "");
        $ips = explode(',', $textIp);
        $ips = array_unique($ips);
        $pos = array_search(' ', $ips);
        if ($pos !== false) {
            unset($ips[$pos]);
        }
        return $ips;
    }

    /**
     * @return bool
     */
    public function isPrivateMode()
    {
        return in_array($this->_requestHttp->getClientIp(), $this->_getPrivateIps());
    }

    /**
     * @return mixed
     */
    public function getProductFaqSorting()
    {
        return $this->scopeConfig->getValue(
            self::XML_PRODUCT_FAQ_SORTING,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
