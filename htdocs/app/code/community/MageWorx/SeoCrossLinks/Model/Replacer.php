<?php
/**
 * MageWorx
 * MageWorx SeoCrossLinks Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoCrossLinks
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoCrossLinks_Model_Replacer
{
    /**
     *
     * @var array
     */
    protected $_productUrlList;

    /**
     *
     * @var array
     */
    protected $_categoryUrlList;

    /**
     * The replaced pairs before converting.
     * The order is important.
     *
     * @var array
     */
    protected $_exceptFromConvert = array(
        '&amp;'  => '!$#amp#$!',
        '& '     => '!$#a#$!',
        '&lt;'   => '!$#lt#$!',
        '&gt;'   => '!$#gt#$!'
    );

    /**
     * Replace keywords to links in html
     *
     * @param MageWorx_SeoCrossLinks_Model_Resource_Crosslink_Collection $collection
     * @param string $html
     * @param int $maxReplaceCount
     * @param string $ignoreProductSku
     * @param int $ignoreCategoryId
     * @return string
     */
    public function replace($collection, $html, $maxReplaceCount, $ignoreProductSku = null, $ignoreCategoryId = null)
    {
        if(!trim($html)){
            return false;
        }
        $preparedHtml = $this->_prepareBeforeConvert($html);
        
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;

        libxml_use_internal_errors(true);
        $dom->loadHTML($preparedHtml);
        libxml_clear_errors();

        $textParts   = array();
        $xpath       = new DOMXPath($dom);
        $domNodeList = $xpath->query('//text()[not(ancestor::script)][not(ancestor::a)]');

        foreach ($domNodeList as $node) {
            if ($node->nodeType === 3) {
                $textParts[] = $node->wholeText;
            }
        }

        if(!count($collection->getItems())){
            return false;
        }

        $this->_specifyCollection($collection, $textParts, $maxReplaceCount);

        $pairs = array();
        $textPartsMod = $this->_dispatchByDestination(
            $collection,
            $textParts,
            $maxReplaceCount,
            $ignoreProductSku,
            $ignoreCategoryId,
            $pairs
        );

        foreach ($domNodeList as $node) {

            if($node->nodeType !== 3){
                continue;
            }

            $replace = array_shift($textPartsMod);
            $newNode = $dom->createDocumentFragment();
            $newNode->appendXML($replace);
            if (is_object($node->parentNode)) {
                $node->parentNode->replaceChild($newNode, $node);
            }
        }
        $convertedHtml = $dom->saveHTML();

        if(!$convertedHtml){
            return false;
        }

        $modifyHtml = str_replace(array_keys($pairs), array_values($pairs), $convertedHtml);

        return $this->_recoveryAfterConvert($modifyHtml);
    }

    /**
     *
     * @param string $html
     * @return string
     */
    protected function _cropWrapper($html)
    {
        $posBodyStart = mb_strpos($html, '<body>');
        $posBodyEnd   = mb_strpos($html, '</body>');

        if($posBodyEnd !== false){
            $html = mb_substr($html, 0, $posBodyEnd);
        }

        if($posBodyStart !== false){
            $html = mb_substr($html, $posBodyStart + 6);
        }

        return $html;
    }

    /**
     * Replaces certain characters
     *
     * @param type $html
     * @return type
     */
    protected function _prepareBeforeConvert($html)
    {
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8");
        return str_replace(array_keys($this->_exceptFromConvert), array_values($this->_exceptFromConvert), $html);
    }

    /**
     * Recovers the characters replaced earlier
     *
     * @param string $html
     * @return string
     */
    protected function _recoveryAfterConvert($html)
    {
        $croppedHtml = $this->_cropWrapper($html);
        return str_replace(array_values($this->_exceptFromConvert), array_keys($this->_exceptFromConvert), $croppedHtml);
    }

    /**
     * Delegate replacements if URL exists
     *
     * @param MageWorx_SeoCrossLinks_Model_Resource_Crosslink_Collection $collection
     * @param array $textParts
     * @param int $maxGlobalCount
     * @param string $ignoreProductSku
     * @param int $ignoreCategoryId
     * @param array $pairs
     * @return array
     */
    protected function _dispatchByDestination(
        $collection,
        $textParts,
        $maxGlobalCount,
        $ignoreProductSku,
        $ignoreCategoryId,
        &$pairs
    ) {
        foreach ($collection as $crosslink) {

            if (!$maxGlobalCount) {
                continue;
            }

            if ($crosslink->getRefProductSku()) {
                $productUrlData = $this->_getProductUrlData($collection, $crosslink, $ignoreProductSku);
                if ($productUrlData) {
                    $textParts = $this->_preliminaryReplaceAndCreateReplacementPairs(
                        $textParts,
                        $crosslink,
                        $productUrlData['url'],
                        $productUrlData['name'],
                        $maxGlobalCount,
                        $pairs
                    );
                }
            }
            elseif ($crosslink->getRefCategoryId()) {
                $categoryUrlData = $this->_getCategoryUrlData($collection, $crosslink, $ignoreCategoryId);
                if ($categoryUrlData) {
                    $textParts = $this->_preliminaryReplaceAndCreateReplacementPairs(
                        $textParts,
                        $crosslink,
                        $categoryUrlData['url'],
                        $categoryUrlData['name'],
                        $maxGlobalCount,
                        $pairs
                    );
                }
            }
            elseif ($crosslink->getRefStaticUrl()) {
                $staticUrl = $this->_getStaticUrl($crosslink);

                if ($staticUrl) {
                    $textParts = $this->_preliminaryReplaceAndCreateReplacementPairs(
                        $textParts,
                        $crosslink,
                        $staticUrl,
                        false,
                        $maxGlobalCount,
                        $pairs
                    );
                }
            }
        }

        return $textParts;
    }

    /**
     * Retrive list of modified text parts ( ...keyword... => ...hash... )
     * Fill $pairs array (hash => URL)
     *
     *
     * @param array $textParts
     * @param MageWorx_SeoCrossLinks_Model_Crosslink $crosslink
     * @param string $url
     * @param int $maxGlobalCount
     * @param array $pairs
     * @return array
     */
    protected function _preliminaryReplaceAndCreateReplacementPairs($textParts, $crosslink, $url, $name, &$maxGlobalCount, &$pairs)
    {
        $replaceCount = 0;
        if ($crosslink->getKeywords()) {

            foreach ($crosslink->getKeywords() as $keyword)
            {
                $availableCount = 1;

                if ($maxGlobalCount == 0) {
                    continue ;
                }

                $pattern        = $this->_getReplacePattern($keyword);
                $href           = $this->_getHtmlHref($crosslink, $keyword, $url, $name);

                for ($i= 0; $i < count($textParts); $i++) {

                    if ($maxGlobalCount == 0) {
                        break 2;
                    }

                    $key = substr(md5(rand()), 0, 7);
                    $res = preg_replace($pattern, $key, $textParts[$i], $availableCount, $replaceCount);

                    if ($res && $replaceCount) {
                        $pairs[$key] = $href;
                        $availableCount -= $replaceCount;
                        $maxGlobalCount -= $replaceCount;
                        $textParts[$i] = $res;
                        break;
                    }
                }
            }
        } else {

            $keyword        = $crosslink->getKeyword();
            $availableCount = min(array($maxGlobalCount, $crosslink->getReplacementCount()));
            $pattern        = $this->_getReplacePattern($keyword);
            $href           = $this->_getHtmlHref($crosslink, $keyword, $url, $name);

            for ($i= 0; $i < count($textParts); $i++) {

                $key = substr(md5(rand()), 0, 7);
                $res = preg_replace($pattern, $key, $textParts[$i], $availableCount, $replaceCount);

                if ($res && $replaceCount) {
                    $pairs[$key] = $href;
                    $availableCount -= $replaceCount;
                    $maxGlobalCount -= $replaceCount;
                    $textParts[$i] = $res;
                }
            }
        }

        return $textParts;
    }

    /**
     * Retrive product URL if it is not current URL/product
     *
     * @param MageWorx_SeoCrossLinks_Model_Resource_Crosslink_Collection $collection
     * @param MageWorx_SeoCrossLinks_Model_Crosslink $crosslink
     * @param string $ignoreProductSku
     * @return string
     */
    protected function _getProductUrlData($collection, $crosslink, $ignoreProductSku)
    {
        if (is_null($this->_productUrlList)) {
            $prodSkuList = array();
            foreach ($collection as $item) {
                if ($item->getRefProductSku() && $item->getRefProductSku() != $ignoreProductSku) {
                    $prodSkuList[] = $item->getRefProductSku();
                }
            }
            $prodUrlResource = Mage::getResourceSingleton('mageworx_seocrosslinks/catalog_product');

            $isUseName = (Mage::helper('mageworx_seocrosslinks')->isUseNameForTitle() !=
                MageWorx_SeoCrossLinks_Model_Source_Title::USE_CROSSLINK_TITLE_ONLY
            );

            $this->_productUrlList = $prodUrlResource->getCollection($prodSkuList, Mage::app()->getStore(), $isUseName);
        }

        if (!empty($this->_productUrlList[$crosslink->getRefProductSku()]['url']) &&
            !$this->_isCurrentUrl($this->_productUrlList[$crosslink->getRefProductSku()]['url'])
        ) {
            return $this->_productUrlList[$crosslink->getRefProductSku()];
        }

        return false;
    }

    /**
     * Retrive category URL if it is not current URL/category
     *
     * @param MageWorx_SeoCrossLinks_Model_Resource_Crosslink_Collection $collection
     * @param MageWorx_SeoCrossLinks_Model_Crosslink $crosslink
     * @param int $ignoreCategoryId
     * @return string
     */
    protected function _getCategoryUrlData($collection, $crosslink, $ignoreCategoryId)
    {
        if (is_null($this->_categoryUrlList)) {
            $catIds = array();
            foreach ($collection as $item) {
                if ($item->getRefCategoryId() && $item->getRefCategoryId() != $ignoreCategoryId) {
                    $catIds[] = $item->getRefCategoryId();
                }
            }
            $catUrlResource = Mage::getResourceSingleton('mageworx_seocrosslinks/catalog_category');
            $this->_categoryUrlList = $catUrlResource->getCollection($catIds, Mage::app()->getStore());
        }

        if (!empty($this->_categoryUrlList[$crosslink->getRefCategoryId()]['url']) &&
            !$this->_isCurrentUrl($this->_categoryUrlList[$crosslink->getRefCategoryId()]['url'])
        ) {
            return $this->_categoryUrlList[$crosslink->getRefCategoryId()];
        }

        return false;
    }

    /**
     * Retrive URL
     *
     * @param MageWorx_SeoCrossLinks_Model_Crosslink $crosslink
     * @return string
     */
    protected function _getStaticUrl($crosslink)
    {
        if (strpos($crosslink->getRefStaticUrl(), '://') === false) {
            $staticUrl =  Mage::app()->getStore()->getUrl() . ltrim(trim($crosslink->getRefStaticUrl()), '/');
        }else{
            $staticUrl = trim($crosslink->getRefStaticUrl());
        }

        if(!$this->_isCurrentUrl($staticUrl)){
            return $staticUrl;
        }
        return false;
    }

    /**
     * Minimize collection using search keywords in text and keyword count
     *
     * @param MageWorx_SeoCrossLinks_Model_Resource_Crosslink_Collection $collection
     * @param array $textParts
     * @param int $maxReplaceAllCount
     */
    protected function _specifyCollection($collection, $textParts, $maxReplaceAllCount)
    {
        $text = implode(' ***mageworx*** ', $textParts);
        $replaceStaticUrlCount = 0;

        foreach ($collection->getItems() as $item) {
            if ($replaceStaticUrlCount > $maxReplaceAllCount)
            {
                $collection->removeItemByKey($item->getCrosslinkId());
                continue;
            }

            $replaceCount = $this->_renderCrossLink($item, $text);
            if ($item->getRefStaticUrl()) {
                $replaceStaticUrlCount += $replaceCount;
            }
        }
    }

    /**
     * Return count of matches or false.
     * Rewrite keyword value for crosslink:
     * if identical match found modify crosslink keyword "cak+" => cake
     * else create keywords property in crosslink model
     *
     * @param MageWorx_SeoCrossLinks_Model_Crosslink $crosslink
     * @param string $text
     * @return int|false
     */
    protected function _renderCrossLink($crosslink, $text)
    {
        if(stripos($text, trim($crosslink->getKeyword(), '+')) !== false){

            $pattern = $this->_getMatchPattern($crosslink->getKeyword());
            $matches = array();
            
            $res = preg_match_all($pattern, $text, $matches);
            if ($res) {
                $cropMatches = array_slice($matches[0], 0, $crosslink->getReplacementCount());

                if (count($cropMatches) == 1) {
                    $crosslink->setKeyword($cropMatches[0]);
                } else {
                    $crosslink->setKeywords($cropMatches);
                }
                return (count($cropMatches));
            }
        }
        return false;
    }

    /**
     * Convert string to regexp
     *
     * @param string $keyword
     * @return string
     */
    protected function _getMatchPattern($keyword)
    {
        $keyword = trim($keyword);
        if (substr($keyword, 0, 1) == '+') {
            $leftPlus = true;
            $keyword  = ltrim($keyword, '+');
        }
        if(substr($keyword, -1, 1) == '+') {
            $rightPlus = true;
            $keyword   = rtrim($keyword, '+');
        }

        $keyword = preg_quote($keyword, '/');

        if(!empty($leftPlus)){
            $keyword = '[^\s.<,]*' . $keyword;
        } else {
            $keyword = '(\b)' . $keyword;
        }

        if(!empty($rightPlus)) {
            $keyword = rtrim($keyword, '+') . '[^\s.<,]*';
        } else {
            $keyword = $keyword . '(\b)';
        }

        return '/' . $keyword . '/iu';
    }

    /**
     * Convert string to regexp
     *
     * @param string $keyword
     * @return string
     */
    protected function _getReplacePattern($keyword)
    {
        return '/(\b)' . preg_quote($keyword, '/') . '(\b)/iu';
    }

    /**
     * Retrive HTML link
     *
     * @param MageWorx_SeoCrossLinks_Model_Crosslink $crosslink
     * @param string $keyword
     * @param string $urlRaw
     * @return string
     */
    protected function _getHtmlHref($crosslink, $keyword, $urlRaw, $name)
    {
        $url     = htmlspecialchars($urlRaw, ENT_COMPAT, 'UTF-8', false);
        $target  = $crosslink->getTargetLinkValue($crosslink->getLinkTarget());

        switch(Mage::helper('mageworx_seocrosslinks')->isUseNameForTitle()){
            case MageWorx_SeoCrossLinks_Model_Source_Title::USE_CROSSLINK_TITLE_ONLY:
                $title = $crosslink->getLinkTitle();
                break;
            case MageWorx_SeoCrossLinks_Model_Source_Title::USE_NAME_IF_EMPTY_TITLE:
                $title = trim($crosslink->getLinkTitle()) ? $crosslink->getLinkTitle() : $name;
                break;
            case MageWorx_SeoCrossLinks_Model_Source_Title::USE_NAME_ALWAYS:
                $title = $name;
                break;
        }

        $title   = htmlspecialchars(strip_tags($title));
        $keyword = htmlspecialchars($keyword);
        
        return "<a class=\"crosslink\" href=\"" . $url . "\" target=\"" . $target . "\" alt=\"" . $title . "\" title=\"" . $title . "\">" . $keyword . "</a>";
    }

    /**
     * Check if input string is current URL
     *
     * @param string $url
     * @return bool
     */
    protected function _isCurrentUrl($url)
    {
        list($currentUrl) = explode('?', Mage::helper('core/url')->getCurrentUrl());

        return (mb_substr($currentUrl, mb_strpos($currentUrl, '://')) == mb_substr($url, mb_strpos($url, '://')));
    }
}