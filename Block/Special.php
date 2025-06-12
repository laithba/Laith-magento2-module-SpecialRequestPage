<?php
namespace Laith\SpecialRequestPage\Block;

use Magento\Framework\View\Element\Template;
use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Store\Model\StoreManagerInterface;

class Special extends Template
{
    protected $blockFactory;
    protected $filterProvider;
    protected $storeManager;

    public function __construct(
        Template\Context $context,
        BlockFactory $blockFactory,
        FilterProvider $filterProvider,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->blockFactory = $blockFactory;
        $this->filterProvider = $filterProvider;
        $this->storeManager = $storeManager;
    }

    public function getCmsBlockHtml($identifier)
    {
        $storeId = $this->storeManager->getStore()->getId();

        $block = $this->blockFactory->create()
            ->setStoreId($storeId)
            ->load($identifier, 'identifier');

        if (!$block->getIsActive()) {
            return '';
        }

        $html = $this->filterProvider->getBlockFilter()
            ->setStoreId($storeId)
            ->filter($block->getContent());

        return $html;
    }
}

