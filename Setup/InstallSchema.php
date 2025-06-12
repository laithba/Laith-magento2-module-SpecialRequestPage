<?php
namespace Laith\SpecialRequestPage\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (!$setup->tableExists('special_request')) {
            $table = $setup->getConnection()->newTable(
                $setup->getTable('special_request')
            )
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Name'
            )
            ->addColumn(
                'email',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Email'
            )
            ->addColumn(
                'message',
                Table::TYPE_TEXT,
                '64k',
                ['nullable' => true],
                'Message'
            )
            ->addColumn(
                'uploaded_files',
                Table::TYPE_TEXT,
                '64k',
                ['nullable' => true],
                'Uploaded Files'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )
            // اذا عندك اعمدة اضافية في Insert لازم تضيفها هنا ايضا:
            ->addColumn(
                'entity_name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Entity Name'
            )
            ->addColumn(
                'phone',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Phone'
            )
            ->setComment('Special Request Table');

            $setup->getConnection()->createTable($table);
        }

        $setup->endSetup();
    }
}

