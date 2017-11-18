<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

require_once 'htmlparser/simple_html_dom.php';

abstract class MageWorx_SeoMarkup_Model_Richsnippet_Abstract
{
    /*
     * Use if _cropScript = true
     */
    const STRING_REPLACED_SCRIPT = "***script_was_here_%number***";

    /*
     * Crop script before parse html and restore after?
     * just in case..
     */
    protected $_cropScript = true;
    protected $_scripts;

    /**
     * @var simple_html_dom
     */
    protected $_html;
    protected $_product;

    /**
     * The list tags for adding of attributes
     * @var array
     */
    protected $_container = array('div', 'p', 'span', 'h1', 'h2', 'h3', 'h4', 'h5', 'ul', 'li');
    protected $_clearFlag;


    /**
     * Sets a condition of compliance of node text content and required value
     * @var string 'equal' | 'partially_equal'
     */
    protected $_complianceCondition = 'equal';

    protected $_nodeKey;

    /**
     * @param simple_html_dom_node $node
     * @return mixed (simple_html_dom_node | false)
     */
    abstract protected function _isValidNode(simple_html_dom_node $node);

    /**
     * @return bool
     */
    abstract protected function _addAttributeForNodes(simple_html_dom_node $node);

    abstract protected function _checkBlockType();

    /**
     * @param simple_html_dom|string $html
     * @param object $block
     * @param bool $clear
     * @return mixed
     */
    public function render($html, $block, $clear = null)
    {
        $microtime = microtime(1);

        $this->_block     = $block;
        $this->_checkBlockType($block);

        $html = $this->_beforeInit($html);
        if (!$html) {
            return false;
        }

        if (!is_object($html)) {
            $this->_html = str_get_html($html);
        }
        elseif ($html instanceof simple_html_dom) {
            $this->_html = $html;
        }
        else {
            return false;
        }

        if (!($this->_html instanceof simple_html_dom)) {
            return false;
        }

        $this->_clearFlag = $clear;

        if (!$this->_beforeRender($html)) {
            return $this->_sendFail($clear);
        }

        if (!$this->_doRender()) {
            return $this->_sendFail($clear);
        }

        if (!$this->_afterRender()) {
            return $this->_sendFail($clear);
        }

        $modifyHtml = $this->_html->outertext;
        $this->_clear();


//        Uncomment for show in frontend result and expended time.
//        echo "<br><font color = green>" . number_format((microtime(1) - $microtime), 5) . " sec need for " . get_class($this) . "</font>";

        return $this->_afterClear($modifyHtml);
    }

    protected function _beforeRender($html)
    {
        return true;
    }

    protected function _beforeInit($html)
    {
        return $this->_cropScriptTags($html);
    }

    protected function _afterClear($html)
    {
        return $this->_restoreScriptTags($html);
    }

    protected function _afterRender()
    {
        return true;
    }

    /**
     * Changes $this->_html
     * @return boolean
     */
    protected function _doRender()
    {

        $nodes = $this->_createPossibleNodesArray();
        if (!count($nodes)) {
            return false;
        }

        $node = $this->_chooseNode($nodes);
        if (!$node) {
            return false;
        }

        if (!$this->_addAttributeForNodes($node)) {
            return false;
        }

        return true;
    }

    /**
     * Retrive array conditions for search. Ex: div[itemtype=http://schema.org/Offer]
     * @link http://simplehtmldom.sourceforge.net/manual.htm
     * @return mixed (array || false)
     */
    protected function _getItemConditions()
    {
        return false;
    }

    /**
     * Values for search in text nodes.
     * @return mixed (array || false)
     */
    protected function _getItemValues()
    {
        return false;
    }

    /**
     * Example: values = array('125.99', '155.99');
     * if '125.99' contains twice and '155.99' contains once in html
     * will be return array contains 3 nodes.
     *
     * @return array
     */
    protected function _createPossibleNodesArray()
    {
        $searchValues     = $this->_getItemValues();
        $searchConditions = $this->_getItemConditions();
        $nodes            = array();

        /** values only for text nodes */
        if (is_array($searchValues) && count($searchValues)) {
            foreach ($searchValues as $key => $value) {
                settype($value, 'string');
                $ret = $this->_html->find('text');
                foreach ($ret as $node) {
                    $search = trim($node->innertext);
                    if ($this->_complianceCondition == 'equal') {
                        if ($search == trim(htmlspecialchars($value))) {
                            $nodes[$key] = $node;
                        }
                    } elseif ($this->_complianceCondition == 'partially_equal') {
                        if (strpos($search, $value) !== false) {
                            $nodes[$key] = $node;
                        }
                    }
                }
            }
        }
        elseif (is_array($searchConditions) && count($searchConditions)) {

            foreach ($searchConditions as $condition) {
                $nodes = array_merge($nodes, $this->_html->find($condition));
            }
        }

        if (!count($nodes)) {
            $this->_nodeNotFound();
        }

        return $nodes;
    }


    /**
     * Return FIRST valid simple_html_dom_node
     * @param array $nodes
     * @return mixed (simple_html_dom_node || false)
     */
    protected function _chooseNode(array $nodes)
    {
        foreach ($nodes as $nodeKey => $node) {
            if ($this->_isValidNode($node)) {
                $this->_nodeKey = $nodeKey;
                return $node;
            }
        }
        $this->_nodeNotFound();
        return false;
    }

    protected function _nodeNotFound()
    {

    }

    protected function _cropScriptTags($html)
    {
        if (!is_object($html) && $this->_cropScript) {
            preg_match_all('#<script[^>]*>.*?</script>#is', $html, $this->_scripts);

            for ($i = 0; $i < count($this->_scripts[0]); $i++) {
                $identificator = str_replace('%number', $i, self::STRING_REPLACED_SCRIPT);
                $html          = str_replace($this->_scripts[0][$i], $identificator, $html);
            }
        }
        return $html;
    }

    protected function _restoreScriptTags($html)
    {
        if ($this->_cropScript) {
            for ($i = 0; $i < count($this->_scripts[0]); $i++) {
                $identificator = str_replace('%number', $i, self::STRING_REPLACED_SCRIPT);
                $html          = str_replace($identificator, "\r\n" . $this->_scripts[0][$i] . "\r\n", $html);
            }
        }
        return $html;
    }

    //***LIBRARY PART***//
    /**
     *
     * @param simple_html_dom_node $node
     * @return simple_html_dom_node | false
     */
    protected function _findParentContainer(simple_html_dom_node $node)
    {
        $node = clone $node;
        while ($node = $node->parent) {
            if (in_array($node->tag, $this->_container)) {
                return $node;
            }
        }
        return false;
    }

    /**
     * Product name node must be neighboring or higher in dom hierarchy than
     * nested nodes as offers, review and others. In other world node
     * should not have nested nodes above itself.
     * @param simple_html_dom_node $node
     * @return boolean
     */
    protected function _isNotInsideTypes(simple_html_dom_node $node, $types = array())
    {
        $node = clone $node;
        while ($node = $node->parent) {
            if (!count($types)) {
                if ($node->itemtype != '') {
                    return false;
                }
            }
            else {
                if (in_array($node->itemtype, $types)) {
                    return false;
                }
            }
        }
        return true;
    }

    protected function _isInsideTypes(simple_html_dom_node $node, array $types)
    {
        $node = clone $node;
        while ($node = $node->parent) {
            foreach ($types as $key => $itemtype) {
                if ($node->itemtype == $itemtype) {
                    unset($types[$key]);
                }
            }
            if (!count($types)) {
                return true;
            }
        }
        return false;
    }

    protected function _sendFail()
    {
        $this->_clear();
        return false;
    }

    protected function _clear()
    {
        if ($this->_clearFlag) {
            $this->_html->clear();
        }
    }

    protected function _setProduct()
    {
        if (is_callable(array($this->_block, 'getProduct'))) {
            $this->_product = $this->_block->getProduct();
        }

        if (!$this->_product && is_object(Mage::registry('current_product'))) {
            $this->_product = Mage::registry('current_product');
        }
    }

    protected function _errorRenderer($message)
    {

    }

}