<?php

/**
 * @author     Kristof Ringleff
 * @package    Fooman_EmailAttachments
 * @copyright  Copyright (c) 2009 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Fooman_EmailAttachments_Helper_Data extends Mage_Core_Helper_Abstract
{

    const LOG_FILE_NAME='fooman_emailattachments.log';

    /**
     * render pdf and attach to email
     *
     * @param        $pdf
     * @param        $mailObj
     * @param string $name
     *
     * @return mixed
     */
    public function addAttachment($pdf, $mailObj, $name = "order")
    {
        try {
            $this->debug('ADDING ATTACHMENT: ' . $name);
            $file = $pdf->render();
            if (!($mailObj instanceof Zend_Mail)) {
                $mailObj = $mailObj->getMail();
            }
            $mailObj->createAttachment(
                $file,
                'application/pdf',
                Zend_Mime::DISPOSITION_ATTACHMENT,
                Zend_Mime::ENCODING_BASE64,
                $name . '.pdf'
            );
            $this->debug('FINISHED ADDING ATTACHMENT: ' . $name);
        } catch (Exception $e) {
            Mage::log('Caught error while attaching pdf:' . $e->getMessage());
        }
        return $mailObj;
    }

    /**
     * attach file to email
     * supported types: pdf
     *
     * @param        $file
     * @param        $mailObj
     *
     * @return mixed
     */
    public function addFileAttachment($file, $mailObj)
    {
        try {
            $this->debug('ADDING ATTACHMENT: ' . $file);
            if (!($mailObj instanceof Zend_Mail)) {
                $mailObj = $mailObj->getMail();
            }
            if (method_exists($mailObj, 'setType')) {
                $mailObj->setType(Zend_Mime::MULTIPART_MIXED);
            }
            $filePath = Mage::getBaseDir('media') . DS . 'pdfs' . DS .$file;
            if (file_exists($filePath)) {
                $mailObj->createAttachment(
                    file_get_contents($filePath),
                    'application/pdf',
                    Zend_Mime::DISPOSITION_ATTACHMENT,
                    Zend_Mime::ENCODING_BASE64,
                    basename($filePath)
                );
            }
            $this->debug('FINISHED ADDING ATTACHMENT: ' . $file);

        } catch (Exception $e) {
            Mage::log('Caught error while attaching pdf:' . $e->getMessage());
        }
        return $mailObj;
    }

    /**
     * attach agreements for store and attach as
     * txt or html to email
     *
     * @param $storeId
     * @param $mailObj
     *
     * @return mixed
     */
    public function addAgreements($storeId, $mailObj)
    {
        $this->debug('ADDING AGREEMENTS');
        $agreements = Mage::getModel('checkout/agreement')->getCollection()
            ->addStoreFilter($storeId)
            ->addFieldToFilter('is_active', 1);
        if ($agreements) {
            foreach ($agreements as $agreement) {
                $agreement->load($agreement->getId());
                $this->debug($agreement->getName());
                $cmsHelper = Mage::helper('cms');
                if (Mage::helper('core')->isModuleEnabled('Fooman_PdfCustomiser')) {
                    $pdf = Mage::getModel('pdfcustomiser/agreement')->getPdf(array($storeId=> $agreement));
                    $this->addAttachment($pdf, $mailObj, $this->_encodedFileName($agreement->getName()));
                } else {
                    $processor = $cmsHelper->getPageTemplateProcessor();
                    $content = $processor->filter($agreement->getContent());
                    if (!($mailObj instanceof Zend_Mail) && !($mailObj instanceof Mandrill_Message)) {
                        $mailObj = $mailObj->getMail();
                    }
                    if ($agreement->getIsHtml()) {
                        $html = '<html><head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        <title>' . $agreement->getName() . '</title></head><body>'
                            . $content . '</body></html>';

                        if ($mailObj instanceof Mandrill_Message) {
                            // Mandrill does not support addAttachment, so use createAttachment in this case
                            $mailObj->createAttachment(
                                $html, 'text/html',
                                Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64,
                                $this->_encodedFileName($agreement->getName() . '.html')
                            );
                        } else {
                            // use addAttachment for Zend_Mail, so that we can set the charset to UTF-8 here
                            $mp = new Zend_Mime_Part($html);
                            $mp->encoding = Zend_Mime::ENCODING_BASE64;
                            $mp->type = 'text/html; charset=UTF-8';
                            $mp->charset = 'UTF-8';
                            $mp->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
                            $mp->filename = $this->_encodedFileName($agreement->getName() . '.html');
                            $mailObj->addAttachment($mp);
                        }
                    } else {
                        $mailObj->createAttachment(
                            Mage::helper('core')->escapeHtml($content), 'text/plain',
                            Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64,
                            $this->_encodedFileName($agreement->getName() . '.txt')
                        );
                    }
                }
                $this->debug('Done ' . $agreement->getName());
            }
        }
        $this->debug('FINISHED ADDING AGREEMENTS');
        return $mailObj;
    }

    /**
     * if in debug mode send message to logs
     *
     * @param $msg
     */
    public function debug($msg)
    {
        if ($this->isDebugMode()) {
            Mage::helper('foomancommon')->sendToFirebug($msg);
            Mage::log($msg, null, self::LOG_FILE_NAME);
        }
    }

    /**
     * are we debugging
     *
     * @return bool
     */
    public function isDebugMode()
    {
        return false;
    }

    /**
     * add print button to block
     *
     * @param $block
     */
    public function addButton($block)
    {
        $block->addButton(
            'fooman_print', array(
                'label'   => Mage::helper('sales')->__('Print'),
                'class'   => 'save',
                'onclick' => 'setLocation(\'' . $this->getPrintUrl($block) . '\')'
            ), 0, 25
        );
    }

    /**
     * return url to print single order from order > view
     *
     * @param void
     * @access protected
     *
     * @return string
     */
    protected function getPrintUrl($block)
    {
        return $block->getUrl(
            'adminhtml/EmailAttachments_order/print',
            array('order_id' => $block->getOrder()->getId())
        );
    }

    /**
     * get array of email addresses
     *
     * @param $configPath
     * @param $storeId
     *
     * @return array|bool
     */
    public function getEmails($configPath, $storeId)
    {
        $data = Mage::getStoreConfig($configPath, $storeId);
        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
    }

    protected function _encodedFileName($subject)
    {
        return sprintf('=?utf-8?B?%s?=', base64_encode($subject));
    }

    public function getOrderAttachmentName($order)
    {
        return Mage::helper('sales')->__('Order') . "_" . $order->getIncrementId();
    }

    public function getInvoiceAttachmentName($invoice)
    {
        return Mage::helper('sales')->__('Invoice') . "_" . $invoice->getIncrementId();
    }

    public function getShipmentAttachmentName($shipment)
    {
        return Mage::helper('sales')->__('Shipment') . "_" . $shipment->getIncrementId();
    }

    public function getCreditmemoAttachmentName($creditmemo)
    {
        return Mage::helper('sales')->__('Credit Memo') . "_" . $creditmemo->getIncrementId();
    }
}
