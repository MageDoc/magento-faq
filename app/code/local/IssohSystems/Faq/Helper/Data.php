<?php

class IssohSystems_Faq_Helper_Data extends Mage_Core_Helper_Abstract
{
    const ACTIVE_TRUE_TXT  = 'Yes';
    const ACTIVE_FALSE_TXT = 'No';


    public function getActiveLabel($value) {
        $array = $this->getActiveOptionArray();

        if (array_key_exists($value, $array)) {
            return $array[$value];
        }
        return $value;
    }

    public function getActiveOptionArray() {
        return $array = array(
            IssohSystems_Faq_Model_Entity::ACTIVE_TRUE  => self::ACTIVE_TRUE_TXT,
            IssohSystems_Faq_Model_Entity::ACTIVE_FALSE => self::ACTIVE_FALSE_TXT
        );
    }

    public function getFontAawesome(){
        $pattern = '/\.(fa-(?:\w+(?:-)?)+):before\s+{\s*content:\s*"\\\\(.+)";\s+}/';
        $fontPath = Mage::getBaseDir('skin'). DS. 'frontend'. DS. 'base'. DS. 'default'. DS. 'faq'. DS. 'css'. DS. 'font-awesome.css';

        $subject =  file_get_contents($fontPath);
        preg_match_all($pattern, $subject, $matches, PREG_SET_ORDER);

        foreach($matches as $match) {
            $icons[$match[1]] = $match[2];
        }

        ksort($icons);
        return $icons;
    }

    public function getDefaultStoreId() {
        return Mage::app()->getWebsite(true)->getDefaultGroup()->getDefaultStoreId();
    }

    public function getFaqUrl() {
        return $this->_getUrl('faq');
    }
}