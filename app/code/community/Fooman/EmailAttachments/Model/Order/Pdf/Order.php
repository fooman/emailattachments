<?php

/**
 * @author     Kristof Ringleff
 * @author     Magento Core Team <core@magentocommerce.com>
 * @package    Fooman_EmailAttachments
 * @copyright  Copyright (c) 2009 Fooman Limited (http://www.fooman.co.nz)
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Fooman_EmailAttachments_Model_Order_Pdf_Order extends Mage_Sales_Model_Order_Pdf_Invoice
{

    /**
     * create a order pdf modelled on the invoice content
     *
     * @param array $orders
     *
     * @return Zend_Pdf
     */
    public function getPdf($orders = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('order');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        $currentStoreId = Mage::app()->getStore()->getId();
        foreach ($orders as $order) {
            //could be order id
            if (!$order instanceof Mage_Sales_Model_Order) {
                $order = Mage::getModel('sales/order')->load($order);
            }

            if ($order->getStoreId()) {
                Mage::getSingleton('customer/address_config')->setStore($order->getStoreId());
                Mage::app()->getLocale()->emulate($order->getStoreId());
                Mage::app()->setCurrentStore($order->getStoreId());
            }
            $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
            $pdf->pages[] = $page;

            /* Add image */
            $this->insertLogo($page, $order->getStore());

            /* Add address */
            $this->insertAddress($page, $order->getStore());

            /* Add head */
            $this->insertOrder(
                $page,
                $order,
                true
            );

            $this->_printTableHead($page);

            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

            $this->_printItems($order, $page);

            /* Add totals */
            $order->setOrder($order);
            $page = $this->insertTotals($page, $order);
            $this->_printComments($order, $page);

            if ($order->getStoreId()) {
                Mage::app()->getLocale()->revert();
            }
        }

        $this->_afterGetPdf();
        Mage::app()->setCurrentStore($currentStoreId);

        return $pdf;
    }

    protected function _printItems($order, $page)
    {
        /* Add body */
        foreach ($order->getAllItems() as $item) {
            if ($item->getParentItem()) {
                continue;
            }

            if ($this->y < 15) {
                $page = $this->newPage(array('table_header' => true));
            }

            /* Draw item */
            //temporarily set item as if we were printing an invoice
            $item->setQty($item->getQtyOrdered());
            $item->setOrderItem($item);
            $page = $this->_drawItem($item, $page, $order);
        }
    }

    protected function _printComments($order, Zend_Pdf_Page $page)
    {
        if (Mage::helper('core')->isModuleEnabled('Magemaven_OrderComment')
            && ($order->getCustomerComment() || $order->getCustomerNote())
        ) {
            $comment = Mage::helper('ordercomment')->escapeHtml(
                $order->getCustomerComment() ? $order->getCustomerComment() : $order->getCustomerNote()
            );
            $this->y -= 15;
            $page->drawText(Mage::helper('ordercomment')->__('Order Comment'), 35, $this->y, 'UTF-8');
            $this->y -= 15;
            $leftToPrint = explode(' ', $comment);
            $availableWidth = $page->getWidth();
            while (!empty($leftToPrint)) {
                $currentLine = $leftToPrint;
                $leftToPrint = array();
                while ($this->widthForStringUsingFontSize(
                        implode(' ', $currentLine), $page->getFont(), $page->getFontSize()
                    ) > $availableWidth) {
                    $leftToPrint[] = array_pop($currentLine);
                }
                $page->drawText(implode(' ', $currentLine), 35, $this->y, 'UTF-8');
            }
        }
    }

    /**
     * Draw header for item table
     *
     * use _drawHeader for Magento 1.7+
     * maintain compatibility with Magento 1.5
     *
     * @param $page
     */
    protected function _printTableHead($page)
    {
        if (method_exists($this, '_drawHeader')) {
            return parent::_drawHeader($page);
        }

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $this->_setFontRegular($page);

        /* Add table */
        $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);

        $page->drawRectangle(25, $this->y, 570, $this->y - 15);
        $this->y -= 10;

        /* Add table head */
        $page->setFillColor(new Zend_Pdf_Color_RGB(0.4, 0.4, 0.4));
        $page->drawText(Mage::helper('sales')->__('Products'), 35, $this->y, 'UTF-8');
        $page->drawText(Mage::helper('sales')->__('SKU'), 255, $this->y, 'UTF-8');
        $page->drawText(Mage::helper('sales')->__('Price'), 380, $this->y, 'UTF-8');
        $page->drawText(Mage::helper('sales')->__('Qty'), 430, $this->y, 'UTF-8');
        $page->drawText(Mage::helper('sales')->__('Tax'), 480, $this->y, 'UTF-8');
        $page->drawText(Mage::helper('sales')->__('Subtotal'), 535, $this->y, 'UTF-8');

        $this->y -= 15;
    }
}
