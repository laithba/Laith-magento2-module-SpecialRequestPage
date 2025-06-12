<?php
namespace Laith\SpecialRequestPage\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ){
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        // ترجع صفحة تحتوي على البلوكات والتيمبليت المرتبطين بالـ layout
        $resultPage = $this->resultPageFactory->create();

        // لو حبيت تعدل على البلوك هنا (اختياري)
        /*
        $block = $resultPage->getLayout()->getBlock('special.request.page');
        if ($block) {
            $block->setSomeData('some value');
        }
        */

        return $resultPage;
    }
}

