<?php

/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
abstract class MageWorx_SeoXTemplates_Model_Converter_Blog extends MageWorx_SeoXTemplates_Model_Converter
{
    protected function __convert($vars, $templateCode)
    {
        $convertValue = $templateCode;

        foreach ($vars as $key => $params) {
            foreach ($params['attributes'] as $attribute) {

                switch ($attribute) {
                    case 'title':
                        $value = $this->_convertTitle();
                        break;
                    case 'created_time':
                        $value = $this->_convertCreatedTime();
                        break;
                    case 'update_time':
                        $value = $this->_convertUpdateTime();
                        break;
                    case 'poster':
                        $value = $this->_convertPoster();
                        break;
                    case 'categories':
                        $value = $this->_convertCategories();
                        break;
                    case 'short_content':
                        $value = $this->_convertShortContent();
                        break;
                    case 'tags':
                        $value = $this->_convertTags();
                        break;
                    default:
                        $value = '';
                        break;
                }

                if ($value) {
                    $value = $params['prefix'] . $value . $params['suffix'];
                    break;
                }
            }
            $convertValue = str_replace($key, $value, $convertValue);
        }

        return $this->_render($convertValue);
    }

    protected function _convertTitle()
    {
        return $this->_item->getTitle();
    }

    protected function _convertCreatedTime()
    {
        return $this->_item->getCreatedTime();
    }

    protected function _convertUpdateTime()
    {
        return $this->_item->getUpdateTime();
    }

    protected function _convertPoster()
    {
        return $this->_item->getUser();
    }

    protected function _convertUpdateUser()
    {
        return $this->_item->getUpdateUser();
    }

    protected function _convertCategories()
    {
        return $this->_item->getCatIds();
    }

    public function _convertShortContent()
    {
        return $this->_item->getShortContent();
    }

    public function _convertTags()
    {
         return $this->_item->getTags();
    }

    protected function _render($convertValue)
    {
        return trim(strip_tags($convertValue));
    }

}
