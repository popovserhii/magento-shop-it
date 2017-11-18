<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_SeoBase_Block_Adminhtml_Renderer_Canonical extends Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element
{
    public function _toHtml() {

        $canonicalUrlString = htmlspecialchars($this->__('Canonical URL'));
        $hintString         = htmlspecialchars($this->__('Switch to Store View Scope to set a canonical tag manually'));

        return '<tr>
        <td class="label"><label for="canonical_url_notice">' . $canonicalUrlString . '</label></td>
        <td class="value">
            <input id="canonical_url_notice" class="required-entry input-text" type="text" readonly="1" style="border:10px" value="' . $hintString .'" name="product[canonical_url_notice]"></td>
        <td class="scope-label"><span class="nobr">[STORE VIEW]</span></td>
        </tr>';
    }
}
