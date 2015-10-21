<?php

/**
 * @author     Kristof Ringleff
 * @package    Fooman_EmailAttachments
 * @copyright  Copyright (c) 2009 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Fooman_EmailAttachments_Model_Selftester extends Fooman_Common_Model_Selftester
{

    /**
     * get extension version information
     */
    public function _getVersions()
    {
        parent::_getVersions();
        $this->messages[] = "Fooman_EmailAttachments Config version: "
            . (string)Mage::getConfig()->getModuleConfig('Fooman_EmailAttachments')->version;
    }

    /**
     * list of extension rewrites
     *
     * @return array
     */
    public function _getRewrites()
    {
        $rewrites = array(
            array("model", "core/email_template_mailer", "Fooman_EmailAttachments_Model_Core_Email_Template_Mailer")
        );
        if ($this->_hasQueueSupport()) {
            array_push($rewrites, array("model", "core/email_queue", "Fooman_EmailAttachments_Model_Core_Email_Queue"));

        }
        return $rewrites;
    }

    /**
     * list of extension files
     *
     * @return array
     */
    public function _getFiles()
    {
        //REPLACE
        return array(
            'app/etc/modules/Fooman_EmailAttachments.xml',
            'app/code/community/Fooman/EmailAttachments/Helper/Data.php',
            'app/code/community/Fooman/EmailAttachments/etc/system.xml',
            'app/code/community/Fooman/EmailAttachments/etc/config.xml',
            'app/code/community/Fooman/EmailAttachments/Block/Adminhtml/Extensioninfo.php',
            'app/code/community/Fooman/EmailAttachments/LICENSE.txt',
            'app/code/community/Fooman/EmailAttachments/controllers/Customer/OrderController.php',
            'app/code/community/Fooman/EmailAttachments/controllers/Adminhtml/EmailAttachments/OrderController.php',
            'app/code/community/Fooman/EmailAttachments/Model/Core/App/Emulation.php',
            'app/code/community/Fooman/EmailAttachments/Model/Core/Email/Template/Mailer.php',
            'app/code/community/Fooman/EmailAttachments/Model/Core/Email/Queue.php',
            'app/code/community/Fooman/EmailAttachments/Model/Core/Email/Queue/Compatibility.php',
            'app/code/community/Fooman/EmailAttachments/Model/Core/Email/Queue/Fooman.php',
            'app/code/community/Fooman/EmailAttachments/Model/Order/Pdf/BundleItems.php',
            'app/code/community/Fooman/EmailAttachments/Model/Order/Pdf/Order.php',
            'app/code/community/Fooman/EmailAttachments/Model/Selftester.php',
            'app/code/community/Fooman/EmailAttachments/Model/Observer.php',
            'app/code/community/Fooman/EmailAttachments/Model/System/File.php',
            'app/locale/hu_HU/Fooman_EmailAttachments.csv',
            'app/locale/pt_BR/Fooman_EmailAttachments.csv',
            'app/locale/gr_GR/Fooman_EmailAttachments.csv',
            'app/locale/he_IL/Fooman_EmailAttachments.csv',
            'app/locale/cs_CZ/Fooman_EmailAttachments.csv',
            'app/locale/zh_CN/Fooman_EmailAttachments.csv',
            'app/locale/da_DK/Fooman_EmailAttachments.csv',
            'app/locale/nb_NO/Fooman_EmailAttachments.csv',
            'app/locale/fa_IR/Fooman_EmailAttachments.csv',
            'app/locale/ko_KR/Fooman_EmailAttachments.csv',
            'app/locale/sl_SI/Fooman_EmailAttachments.csv',
            'app/locale/ca_ES/Fooman_EmailAttachments.csv',
            'app/locale/es_ES/Fooman_EmailAttachments.csv',
            'app/locale/tr_TR/Fooman_EmailAttachments.csv',
            'app/locale/ar_SA/Fooman_EmailAttachments.csv',
            'app/locale/lt_LT/Fooman_EmailAttachments.csv',
            'app/locale/th_TH/Fooman_EmailAttachments.csv',
            'app/locale/no_NO/Fooman_EmailAttachments.csv',
            'app/locale/hr_HR/Fooman_EmailAttachments.csv',
            'app/locale/ja_JP/Fooman_EmailAttachments.csv',
            'app/locale/et_EE/Fooman_EmailAttachments.csv',
            'app/locale/ru_RU/Fooman_EmailAttachments.csv',
            'app/locale/lv_LV/Fooman_EmailAttachments.csv',
            'app/locale/fi_FI/Fooman_EmailAttachments.csv',
            'app/locale/en_US/template/email/sales/order_new_packingslip.html',
            'app/locale/en_US/Fooman_EmailAttachments.csv',
            'app/locale/ro_RO/Fooman_EmailAttachments.csv',
            'app/locale/de_DE/Fooman_EmailAttachments.csv',
            'app/locale/fr_FR/Fooman_EmailAttachments.csv',
            'app/locale/nl_NL/Fooman_EmailAttachments.csv',
            'app/locale/it_IT/Fooman_EmailAttachments.csv',
            'app/locale/sk_SK/Fooman_EmailAttachments.csv',
            'app/locale/sv_SE/Fooman_EmailAttachments.csv',
            'app/locale/pl_PL/Fooman_EmailAttachments.csv',
        );
        //REPLACE_END
    }

    protected function _hasQueueSupport()
    {
        return file_exists(
            Mage::getConfig()->getModuleDir('', 'Mage_Core') . DS . 'Model' . DS . 'Email' . DS . 'Queue.php'
        );
    }

    public function _needsCron()
    {
        if ($this->_hasQueueSupport()) {
            return true;
        } else {
            return false;
        }
    }
}


