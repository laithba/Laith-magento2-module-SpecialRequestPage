<?php
namespace Laith\SpecialRequestPage\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Filesystem\Io\File as IoFile;
use Magento\Framework\Controller\Result\RedirectFactory;

class Save extends Action
{
    protected $fileDriver;
    protected $ioFile;
    protected $resultRedirectFactory;

    public function __construct(
        Context $context,
        FileDriver $fileDriver,
        IoFile $ioFile,
        RedirectFactory $resultRedirectFactory
    ) {
        parent::__construct($context);
        $this->fileDriver = $fileDriver;
        $this->ioFile = $ioFile;
        $this->resultRedirectFactory = $resultRedirectFactory;
    }

    public function execute()
    {
        $post = $this->getRequest()->getPostValue();

        if (!$post) {
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }

        try {
            // مسار ملف اللوج داخل var/logs/
            $logPath = BP . '/var/log';
            $logFile = $logPath . '/laith_log.log';

            // تأكد ان المجلد موجود، إذا مش موجود أنشئه
            if (!$this->fileDriver->isDirectory($logPath)) {
                $this->ioFile->mkdir($logPath, 0755);
            }

            // نص الرسالة اللي بدك تكتبها
            $logMessage = date('Y-m-d H:i:s') . " - SpecialRequestPage Form Data: " . print_r($post, true) . PHP_EOL;

            // اكتب في الملف (لو الملف مش موجود ينشئه)
            $this->fileDriver->filePutContents($logFile, $logMessage, FILE_APPEND);

            $this->messageManager->addSuccessMessage(__('Your special request has been submitted successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error occurred while submitting your request.'));
            // لو بدك تسجل الخطأ هنا بنفس الطريقة أو تستدعي logger الافتراضي
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}

