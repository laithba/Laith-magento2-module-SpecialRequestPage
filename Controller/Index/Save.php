<?php
namespace Laith\SpecialRequestPage\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Filesystem\Io\File as IoFile;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResourceConnection;

class Save extends Action
{
    protected $fileDriver;
    protected $ioFile;
    protected $resultRedirectFactory;
    protected $uploaderFactory;
    protected $filesystem;
    protected $resource;

    public function __construct(
        Context $context,
        FileDriver $fileDriver,
        IoFile $ioFile,
        RedirectFactory $resultRedirectFactory,
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem,
        ResourceConnection $resource
    ) {
        parent::__construct($context);
        $this->fileDriver = $fileDriver;
        $this->ioFile = $ioFile;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->resource = $resource;
    }

    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$post) {
            return $resultRedirect->setPath('*/*/');
        }

        $uploadedFileNames = [];

        try {
            if (!empty($_FILES['attachment']['name'][0])) {
                $mediaDir = $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR);
                $target = $mediaDir->getAbsolutePath('uploads/special_request/');

                if (!$this->fileDriver->isDirectory($target)) {
                    $this->ioFile->mkdir($target, 0755, true);
                }

                foreach ($_FILES['attachment']['name'] as $key => $name) {
                    try {
                        $fileArray = [
                            'name' => $_FILES['attachment']['name'][$key],
                            'type' => $_FILES['attachment']['type'][$key],
                            'tmp_name' => $_FILES['attachment']['tmp_name'][$key],
                            'error' => $_FILES['attachment']['error'][$key],
                            'size' => $_FILES['attachment']['size'][$key]
                        ];

                        $uploader = $this->uploaderFactory->create(['fileId' => $fileArray]);
                        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif', 'pdf', 'xls', 'xlsx', 'doc', 'docx']);
                        $uploader->setAllowRenameFiles(true);
                        $uploader->setFilesDispersion(false);

                        $result = $uploader->save($target);
                        if (isset($result['file'])) {
                            // خزّن فقط اسم الملف وليس المسار الكامل
                            $uploadedFileNames[] = $result['file'];
                        }
                    } catch (\Exception $e) {
                        $this->logError('File Upload Error: ' . $e->getMessage());
                    }
                }
            }

            $connection = $this->resource->getConnection();
            $tableName = $this->resource->getTableName('special_request');

            $data = [
                'name' => $post['fullName'] ?? '',
                'email' => $post['email'] ?? '',
                'message' => $post['specialRequest'] ?? '',
                'uploaded_files' => !empty($uploadedFileNames) ? implode(',', $uploadedFileNames) : null,
                'created_at' => date('Y-m-d H:i:s'),
                // إذا الجدول عنده أعمدة entityName و phone لازم تضيفهم في الـ InstallSchema.php أيضاً
                'entity_name' => $post['entityName'] ?? '',
                'phone' => $post['phone'] ?? ''
            ];

            $connection->insert($tableName, $data);

            // سجل البيانات في ملف اللوج
            $logPath = BP . '/var/log';
            $logFile = $logPath . '/laith_log.log';

            if (!$this->fileDriver->isDirectory($logPath)) {
                $this->ioFile->mkdir($logPath, 0755);
            }

            $logMessage = date('Y-m-d H:i:s') . " - SpecialRequestPage Form Data: " . print_r($post, true);
            if (!empty($uploadedFileNames)) {
                $logMessage .= " Uploaded files: " . implode(', ', $uploadedFileNames) . PHP_EOL;
            } else {
                $logMessage .= " No files uploaded." . PHP_EOL;
            }

            $this->fileDriver->filePutContents($logFile, $logMessage, FILE_APPEND);

            $this->messageManager->addSuccessMessage(__('Your special request has been submitted successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error occurred while submitting your request.'));
            $this->logError('Main Controller Error: ' . $e->getMessage());
        }

        return $resultRedirect->setPath('*/*/');
    }

    protected function logError($message)
    {
        $logPath = BP . '/var/log/laith_error.log';
        $errorMessage = date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL;
        $this->fileDriver->filePutContents($logPath, $errorMessage, FILE_APPEND);
    }
}

