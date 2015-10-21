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
class Fooman_EmailAttachments_Model_Order_Pdf_BundleItems extends Mage_Bundle_Model_Sales_Order_Pdf_Items_Invoice
{
    /**
     * @param Varien_Object $item
     *
     * @return null
     */
    public function getChilds($item)
    {

        $_itemsArray = array();

        if ($item instanceof Mage_Sales_Model_Order_Item) {
            $_items = $item->getOrder()->getAllItems();
        }

        if ($_items) {
            foreach ($_items as $_item) {
                $parentItem = $_item->getParentItem();
                $_item->setOrderItem($_item);
                if ($parentItem) {
                    $_itemsArray[$parentItem->getId()][$_item->getId()] = $_item;
                } else {
                    $_itemsArray[$_item->getId()][$_item->getId()] = $_item;
                }
            }
        }

        if (isset($_itemsArray[$item->getId()])) {
            return $_itemsArray[$item->getId()];
        } else {
            return null;
        }
    }
}

