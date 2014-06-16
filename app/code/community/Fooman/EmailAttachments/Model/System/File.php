<?php

/**
 * @author     Kristof Ringleff
 * @package    Fooman_EmailAttachments
 * @copyright  Copyright (c) 2009 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Fooman_EmailAttachments_Model_System_File extends Mage_Adminhtml_Model_System_Config_Backend_File
{
    /**
     * supply allowed file extensions
     *
     * @return array
     */
    protected function _getAllowedExtensions()
    {
        return array('pdf');
    }

}