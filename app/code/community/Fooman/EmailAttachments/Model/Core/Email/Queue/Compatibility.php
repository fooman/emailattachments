<?php

if (Mage::helper('core')->isModuleEnabled('Aschroder_SMTPPro')
    && version_compare(
        (string)Mage::getConfig()->getNode()->modules->Aschroder_SMTPPro->version,
        '2.0.6', '>'
    )
) {
    class Fooman_EmailAttachments_Model_Core_Email_Queue_Compatibility
        extends Aschroder_SMTPPro_Model_Email_Queue
    {

    }
} else {
    class Fooman_EmailAttachments_Model_Core_Email_Queue_Compatibility
        extends Fooman_EmailAttachments_Model_Core_Email_Queue_Fooman
    {

    }
}


