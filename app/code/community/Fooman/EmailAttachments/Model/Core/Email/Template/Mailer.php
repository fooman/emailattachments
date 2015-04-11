<?php

/**
 * @author     Kristof Ringleff
 * @package    Fooman_EmailAttachments
 * @copyright  Copyright (c) 2009 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Fooman_EmailAttachments_Model_Core_Email_Template_Mailer extends Mage_Core_Model_Email_Template_Mailer
{
    /**
     * override to add an event before email is being sent off
     *
     * @return Fooman_EmailAttachments_Model_Core_Email_Template_Mailer
     */
    public function send()
    {
        $helper = Mage::helper('emailattachments');
        // Send all emails from corresponding list
        while (!empty($this->_emailInfos)) {
            $emailTemplate = Mage::getModel('core/email_template');
            $emailInfo = array_pop($this->_emailInfos);
            $helper->debug('NEW EMAIL------------------------------------------');
            $helper->debug($emailInfo->getToNames());
            $this->dispatchAttachEvent($emailTemplate, $emailInfo);
            // Handle "Bcc" recepients of the current email
            $emailTemplate->addBcc($emailInfo->getBccEmails());
            //support queuing on newer Magento versions
            $emailTemplate->setQueue($this->getQueue());
            // Set required design parameters and delegate email sending to Mage_Core_Model_Email_Template
            $emailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $this->getStoreId()))
                ->sendTransactional(
                    $this->getTemplateId(),
                    $this->getSender(),
                    $emailInfo->getToEmails(),
                    $emailInfo->getToNames(),
                    $this->getTemplateParams(),
                    $this->getStoreId()
                );
            $helper->debug('FINISHED SENDING');
        }
        return $this;
    }

    /**
     * handle dispatching of events based on template being sent
     *
     * @param $emailTemplate
     * @param $emailInfo
     */
    public function dispatchAttachEvent($emailTemplate, $emailInfo)
    {
        $storeId = $this->getStoreId();
        $templateParams = $this->getTemplateParams();

        //compare template id to work out what we are currently sending
        switch ($this->getTemplateId()) {

            //Order
            case Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_TEMPLATE, $storeId):
            case Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId):
                Mage::dispatchEvent(
                    'fooman_emailattachments_before_send_order',
                    array(
                         'update'     => false,
                         'template'   => $emailTemplate,
                         'object'     => $templateParams['order'],
                         'email_info' => $emailInfo
                    )
                );
                break;
            //Order Updates
            case Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_UPDATE_EMAIL_TEMPLATE, $storeId):
            case Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE, $storeId):
                Mage::dispatchEvent(
                    'fooman_emailattachments_before_send_order',
                    array(
                         'update'     => true,
                         'template'   => $emailTemplate,
                         'object'     => $templateParams['order'],
                         'email_info' => $emailInfo
                    )
                );
                break;

            //Invoice
            case Mage::getStoreConfig(Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_TEMPLATE, $storeId):
            case Mage::getStoreConfig(Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId):
                Mage::dispatchEvent(
                    'fooman_emailattachments_before_send_invoice',
                    array(
                         'update'     => false,
                         'template'   => $emailTemplate,
                         'object'     => $templateParams['invoice'],
                         'email_info' => $emailInfo
                    )
                );
                break;

            //Invoice Updates
            case Mage::getStoreConfig(Mage_Sales_Model_Order_Invoice::XML_PATH_UPDATE_EMAIL_TEMPLATE, $storeId):
            case Mage::getStoreConfig(Mage_Sales_Model_Order_Invoice::XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE, $storeId):
                Mage::dispatchEvent(
                    'fooman_emailattachments_before_send_invoice',
                    array(
                         'update'     => true,
                         'template'   => $emailTemplate,
                         'object'     => $templateParams['invoice'],
                         'email_info' => $emailInfo
                    )
                );
                break;

            //Shipment
            case Mage::getStoreConfig(Mage_Sales_Model_Order_Shipment::XML_PATH_EMAIL_TEMPLATE, $storeId):
            case Mage::getStoreConfig(Mage_Sales_Model_Order_Shipment::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId):
                Mage::dispatchEvent(
                    'fooman_emailattachments_before_send_shipment',
                    array(
                         'update'     => false,
                         'template'   => $emailTemplate,
                         'object'     => $templateParams['shipment'],
                         'email_info' => $emailInfo
                    )
                );
                break;

            //Shipment Updates
            case Mage::getStoreConfig(Mage_Sales_Model_Order_Shipment::XML_PATH_UPDATE_EMAIL_TEMPLATE, $storeId):
            case Mage::getStoreConfig(Mage_Sales_Model_Order_Shipment::XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE, $storeId):
                Mage::dispatchEvent(
                    'fooman_emailattachments_before_send_shipment',
                    array(
                         'update'     => true,
                         'template'   => $emailTemplate,
                         'object'     => $templateParams['shipment'],
                         'email_info' => $emailInfo
                    )
                );
                break;

            //Creditmemo
            case Mage::getStoreConfig(Mage_Sales_Model_Order_Creditmemo::XML_PATH_EMAIL_TEMPLATE, $storeId):
            case Mage::getStoreConfig(Mage_Sales_Model_Order_Creditmemo::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId):
                Mage::dispatchEvent(
                    'fooman_emailattachments_before_send_creditmemo',
                    array(
                         'update'     => false,
                         'template'   => $emailTemplate,
                         'object'     => $templateParams['creditmemo'],
                         'email_info' => $emailInfo
                    )
                );
                break;

            //Creditmemo Updates
            case Mage::getStoreConfig(Mage_Sales_Model_Order_Creditmemo::XML_PATH_UPDATE_EMAIL_TEMPLATE, $storeId):
            case Mage::getStoreConfig(
                Mage_Sales_Model_Order_Creditmemo::XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE, $storeId
            ):
                Mage::dispatchEvent(
                    'fooman_emailattachments_before_send_creditmemo',
                    array(
                         'update'     => true,
                         'template'   => $emailTemplate,
                         'object'     => $templateParams['creditmemo'],
                         'email_info' => $emailInfo
                    )
                );
                break;
            default:
                Mage::dispatchEvent(
                    'fooman_emailattachments_before_send',
                    array(
                         'template'   => $emailTemplate,
                         'params'     => $templateParams,
                         'email_info' => $emailInfo
                    )
                );
        }
    }

    public function sendMageEvent($name, $update, $emailTemplate, $object, $emailInfo)
    {
        Mage::dispatchEvent(
            $name,
            array(
                 'update'     => $update,
                 'template'   => $emailTemplate,
                 'object'     => $object,
                 'email_info' => $emailInfo
            )
        );
    }
}
