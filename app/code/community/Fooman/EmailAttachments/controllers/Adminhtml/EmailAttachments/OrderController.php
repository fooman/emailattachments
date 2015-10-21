<?php
require_once BP . '/app/code/core/Mage/Adminhtml/controllers/Sales/OrderController.php';

/**
 * @author     Kristof Ringleff
 * @package    Fooman_EmailAttachments
 * @copyright  Copyright (c) 2009 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Fooman_EmailAttachments_Adminhtml_EmailAttachments_OrderController extends Mage_Adminhtml_Sales_OrderController
{

    /**
     * send order pdfs for given order_id
     *
     * @return Mage_Core_Controller_Varien_Action
     */
    public function printAction()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if ($orderId) {
            /* @var $order Mage_Sales_Model_Order */
            $order = Mage::getModel('sales/order')->load($orderId);
            if ($order) {
                if ($order->getStoreId()) {
                    Mage::app()->setCurrentStore($order->getStoreId());
                }
                $pdf = Mage::getModel('emailattachments/order_pdf_order')->getPdf(array($order));

                return $this->_prepareDownloadResponse(
                    'order' . Mage::getSingleton('core/date')->date('Y-m-d_H-i-s') . '.pdf',
                    $pdf->render(),
                    'application/pdf'
                );
            }
        } else {
            $this->_getSession()->addError(
                $this->__('There are no printable documents related to selected orders')
            );
        }
        $this->_redirect('*/*/');
    }

    /**
     * send order pdfs for given order_ids
     *
     * @return Mage_Core_Controller_Varien_Action
     */
    public function pdfordersAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids');
        $flag = false;
        if (!empty($orderIds)) {
            foreach ($orderIds as $orderId) {
                /* @var $order Mage_Sales_Model_Order */
                $order = Mage::getModel('sales/order')->load($orderId);
                if ($order->getId()) {
                    $flag = true;
                    if (!isset($pdf)) {
                        $pdf = Mage::getModel('emailattachments/order_pdf_order')
                            ->getPdf(array($order));
                    } else {
                        $pages = Mage::getModel('emailattachments/order_pdf_order')
                            ->getPdf(array($order));
                        $pdf->pages = array_merge($pdf->pages, $pages->pages);
                    }
                }
            }
        }
        if ($flag) {
            return $this->_prepareDownloadResponse(
                'order' . Mage::getSingleton('core/date')->date('Y-m-d_H-i-s') . '.pdf',
                $pdf->render(),
                'application/pdf'
            );
        } else {
            $this->_getSession()->addError(
                $this->__('There are no printable documents related to selected orders')
            );
        }
        $this->_redirect('*/*');
    }
}
