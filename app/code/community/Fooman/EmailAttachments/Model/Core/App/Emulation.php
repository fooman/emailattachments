<?php

class Fooman_EmailAttachments_Model_Core_App_Emulation extends Mage_Core_Model_App_Emulation

{
    /**
     * rewrite class to bring back previous behaviour
     * to always reload locale on app emulation
     * since it can cause a bug when app emulation is triggered via
     * a cron job not loading non-english translations
     *
     * Apply locale of the specified store
     *
     * @param integer $storeId
     * @param string  $area
     *
     * @return string initial locale code
     */
    protected function _emulateLocale($storeId, $area = Mage_Core_Model_App_Area::AREA_FRONTEND)
    {
        if (php_sapi_name() == 'cli' && property_exists($this, '_app')) {
            $initialLocaleCode = $this->_app->getLocale()->getLocaleCode();
            $newLocaleCode = $this->_getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $storeId);
            $this->_app->getLocale()->setLocaleCode($newLocaleCode);
            $this->_factory->getSingleton('core/translate')->setLocale($newLocaleCode)->init($area, true);
            return $initialLocaleCode;
        } else {
            return parent::_emulateLocale($storeId, $area);
        }
    }
}
