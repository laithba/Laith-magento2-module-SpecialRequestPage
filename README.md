# 🚀 Laith_SpecialRequestPage Magento 2 Extension

## 📋 OVERVIEW

This Magento 2 extension provides a **Special Request** form functionality, allowing customers to submit special requests along with optional file attachments (images, documents, etc.). Submitted requests are stored in a custom database table and logged for reference.

The extension supports multiple file uploads and stores files under the `var/uploads/special_request/` directory.

---

## ⚙️ INSTALLATION

1. Copy the module folder `Laith/SpecialRequestPage` into your Magento root `app/code` directory:


2. Run the following Magento CLI commands from the root of your Magento installation:

```bash
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
bin/magento cache:flush
**Make sure the module is enabled:**
bin/magento module:enable Laith_SpecialRequestPage
-------------------------------------------------------------
🛠️ USAGE
Backend Block Configuration
To display the special request form on your frontend page, you need to create a CMS block in the Magento Admin panel.

The CMS block identifier used in the extension is lt12 for the main content and laimg for the image block.

You can customize the content of these blocks via Content > Blocks in the Admin.

Frontend Template
The frontend template special.phtml renders the form and fetches the CMS blocks using the identifiers mentioned above.

The form posts to the controller action special-price/index/save which handles the data submission.
🛠️ PLEASE LOOK 🛠️
🧱 Step 1: Create CMS Blocks
To render the form properly, you need to create two CMS blocks in the Magento Admin under:

Content → Blocks → Add New Block

Block 1 – Main Content (Form Title/Description)
Identifier: lt12

Content: Add any introductory text or instructions for the special request form.

Block 2 – Image or Sidebar Content
Identifier: laimg

Content: You can add a promotional image, custom message, or leave it blank.

☝️ These two blocks will be fetched and rendered in the frontend using their identifiers.

🎨 Step 2: Edit CMS Page (Optional)
If you want the form to appear on a CMS page (like /special-request), do the following:

Go to Content → Pages.

Create or edit a page.

Use the following content in the WYSIWYG:


{{block class="Laith\SpecialRequestPage\Block\Special" template="Laith_SpecialRequestPage::special.phtml"}}
🧾 Data Submission
The form posts to: /special-price/index/save

Uploaded files will be saved in: var/uploads/special_request/

Logs are written to: var/log/laith_log.log



📝 NOTES
Uploaded files are saved to the directory: var/uploads/special_request/.

Logs related to form submission are written to: var/log/laith_log.log.

Make sure the var directory and its subdirectories have proper write permissions.

You may need to add additional CMS blocks or modify existing templates depending on your theme to display the special request form properly.

🤝 SUPPORT
If you encounter any issues or have questions, please open an issue on the GitHub repository.


