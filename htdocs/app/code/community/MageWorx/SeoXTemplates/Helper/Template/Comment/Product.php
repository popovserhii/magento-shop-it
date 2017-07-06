<?php
/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoXTemplates_Helper_Template_Comment_Product extends MageWorx_SeoXTemplates_Helper_Template_Comment
{
    /**
     * Retrive comment for template edit page
     * @param int $typeId
     * @return string
     * @throws Exception
     */
    public function getComment($typeId)
    {
        if(Mage::helper('mageworx_seoxtemplates/config')->isShowCommentAboutCategory()){
            $additionalComment = '<p><font color = "#ea7601">Note: The variables [category] and [categories] should be used when categories are added in product path only to avoid duplicates in meta tags.</font>';
        }else{
            $additionalComment = '<p><font color = "#ea7601">Note: The variables [category] and [categories] are replaced dynamically on the front-end only.</font>';
        }

        $comment = '';
        switch($typeId){
            case MageWorx_SeoXTemplates_Helper_Template_Product::PRODUCT_NAME:
                $comment = '<p><p><b>Template variables</b><br>
                    <p>[attribute] — e.g. [name], [price], [manufacturer], [color] — will be replaced with the respective product attribute value or removed if value is not available<br>
                    <p>[attribute1|attribute2|...] — e.g. [manufacturer|brand] — if the first attribute value is not available for the product the second will be used and so on untill it finds a value<br>
                    <p>[prefix {attribute} suffix] or<br>
                    <p>[prefix {attribute1|attribute2|...} suffix] — e.g. [({color} color)] — if an attribute value is available it will be prepended with prefix and appended with suffix, either prefix or suffix can be used alone. <br>
                    <p>Randomizer feature is available. The construction like [Buy||Order||Purchase] will use a randomly picked word for each next item when applying a template.<br>
                    Also randomizers can be used within other template variables, ex: [for only||for {price}]. Number of randomizers blocks is not limited within the template.<br>
                    <p><b>Example</b><p>[name][ by {manufacturer|brand}][ of {color} color][ for||for only {price}] <p>will be transformed into<br>
                    <p>HTC Touch Diamond by HTC of Black color for only € 517.50<br>
                    <p>';
                break;
            case MageWorx_SeoXTemplates_Helper_Template_Product::PRODUCT_URL_KEY:
                $comment = '<p><p><b>Hint:</b> Try not to change product URL key templates many times because each time you change and apply a new product URL key template, Magento creates redirects for all your products from old URLs to new ones.<br>
                    <p><b>Hint:</b> Try to build the product URL key template to get unique product URLs. Otherwise, Magento will add numbers to the product URLs. Example: you may add the product SKU to the URLs.<br>
                    <p><p><b>Template variables</b><br>
                    <p>[attribute] — e.g. [name], [price], [manufacturer], [color] — will be replaced with the respective product attribute value or removed if value is not available<br>
                    <p>[attribute1|attribute2|...] — e.g. [manufacturer|brand] — if the first attribute value is not available for the product the second will be used and so on untill it finds a value<br>
                    <p>[prefix {attribute} suffix] or<br>
                    <p>[prefix {attribute1|attribute2|...} suffix] — e.g. [({color} color)] — if an attribute value is available it will be prepended with prefix and appended with suffix, either prefix or suffix can be used alone. <br>
                    <p><b>Example</b><p>[name][ by {manufacturer|brand}][ {color} color][ for {price}] <p>will be transformed into<br>
                    <p>htc-touch-diamond-by-htc-black-color-for-517-50<br>
                    <p>';
                break;
            case MageWorx_SeoXTemplates_Helper_Template_Product::PRODUCT_SHORT_DESCRIPTION:
                $comment = '<p><p><b>Template variables</b><br>
                    <p>[attribute] — e.g. [name], [price], [manufacturer], [color] — will be replaced with the respective product attribute value or removed if value is not available<br>
                    <p>[attribute1|attribute2|...] — e.g. [manufacturer|brand] — if the first attribute value is not available for the product the second will be used and so on untill it finds a value<br>
                    <p>[prefix {attribute} suffix] or<br>
                    <p>[prefix {attribute1|attribute2|...} suffix] — e.g. [({color} color)] — if an attribute value is available it will be prepended with prefix and appended with suffix, either prefix or suffix can be used alone<br>
                    <p>Additional variables available for Product Meta Title and Description only: [category], [categories], [store_name], [website_name]<br>
                    <p>Randomizer feature is available. The construction like [Buy||Order||Purchase] will use a randomly picked word for each next item when applying a template.<br>
                    Also randomizers can be used within other template variables, ex: [for only||for {price}]. Number of randomizers blocks is not limited within the template.<br>
                    <p><b>Example</b><br>
                    <p>[Buy||Order] [name] [by {manufacturer|brand}] [of {color} color] [for only||for {price}] [in {categories}] at [{store_name},] [website_name]. [short_description] will be transformed into<br>
                    <p>Order HTC Touch Diamond by HTC of Black color for only € 517.50 in Cell Phones - Electronics at Digital Store, Digital-Store.com. HTC Touch Diamond signals a giant leap forward in combining hi-tech prowess with intuitive usability and exhilarating design.';
                break;
            case MageWorx_SeoXTemplates_Helper_Template_Product::PRODUCT_DESCRIPTION:
                $comment = '<p><p><b>Template variables</b><br>
                    <p>[attribute] — e.g. [name], [price], [manufacturer], [color] — will be replaced with the respective product attribute value or removed if value is not available<br>
                    <p>[attribute1|attribute2|...] — e.g. [manufacturer|brand] — if the first attribute value is not available for the product the second will be used and so on untill it finds a value<br>
                    <p>[prefix {attribute} suffix] or<br>
                    <p>[prefix {attribute1|attribute2|...} suffix] — e.g. [({color} color)] — if an attribute value is available it will be prepended with prefix and appended with suffix, either prefix or suffix can be used alone.<br>
                    <p>Randomizer feature is available. The construction like [Buy||Order||Purchase] will use a randomly picked word for each next item when applying a template.<br>
                    Also randomizers can be used within other template variables, ex: [for only||for {price}]. Number of randomizers blocks is not limited within the template.<br>
                    <p><b>Example</b><br>
                    <p>[Buy||Order] [name][ by {manufacturer|brand}][ of {color} color][ for only||for {price}.][ short_description] <p>will be transformed into<br>
                    <p>Buy HTC Touch Diamond by HTC of Black color for € 517.50. HTC Touch Diamond signals a giant leap forward in combining hi-tech prowess with intuitive usability and exhilarating design';
                break;
            case MageWorx_SeoXTemplates_Helper_Template_Product::PRODUCT_META_TITLE:
                $comment = '<p><p><b>Template variables</b><br>
                    <p>[attribute] — e.g. [name], [price], [manufacturer], [color] — will be replaced with the respective product attribute value or removed if value is not available<br>
                    <p>[attribute1|attribute2|...] — e.g. [manufacturer|brand] — if the first attribute value is not available for the product the second will be used and so on untill it finds a value<br>
                    <p>[prefix {attribute} suffix] or<br>
                    <p>[prefix {attribute1|attribute2|...} suffix] — e.g. [({color} color)] — if an attribute value is available it will be prepended with prefix and appended with suffix, either prefix or suffix can be used alone.<br>
                    <p>Additional variables available for Product Meta Title: [category], [categories], [store_name], [website_name]<br>
                    ' . $additionalComment .'
                    <p>Randomizer feature is available. The construction like [Buy||Order||Purchase] will use a randomly picked word for each next item when applying a template.<br>
                    Also randomizers can be used within other template variables, ex: [for only||for {price}]. Number of randomizers blocks is not limited within the template.<br>
                    <p><b>Example</b><br>
                    <p>[name][from||by {manufacturer|brand}][ ({color} color)][ for||for special {price}][ in {categories}] <p>will be transformed into<br>
                    <p>HTC Touch Diamond by HTC (Black color) for € 517.50 in Cell Phones - Electronics';
                break;
            case MageWorx_SeoXTemplates_Helper_Template_Product::PRODUCT_META_DESCRIPTION:
                $comment = '<p><p><b>Template variables</b><br>
                    <p>[attribute] — e.g. [name], [price], [manufacturer], [color] — will be replaced with the respective product attribute value or removed if value is not available<br>
                    <p>[attribute1|attribute2|...] — e.g. [manufacturer|brand] — if the first attribute value is not available for the product the second will be used and so on untill it finds a value<br>
                    <p>[prefix {attribute} suffix] or<br>
                    <p>[prefix {attribute1|attribute2|...} suffix] — e.g. [({color} color)] — if an attribute value is available it will be prepended with prefix and appended with suffix, either prefix or suffix can be used alone.<br>
                    <p>Additional variables available for Product Meta Description: [category], [categories], [store_name], [website_name]<br>
                    ' . $additionalComment . '
                    <p>Randomizer feature is available. The construction like [Buy||Order||Purchase] will use a randomly picked word for each next item when applying a template.<br>
                    Also randomizers can be used within other template variables, ex: [for only||for {price}]. Number of randomizers blocks is not limited within the template.<br>
                    <p><b>Example</b><br>
                    <p>[Buy||Order] [name][ by {manufacturer|brand}][ of {color} color][ for only||for {price}][ in {categories}] at[ {store_name},][ website_name]. [short_description] <p>will be transformed into<br>
                    <p>Order HTC Touch Diamond by HTC of Black color for only € 517.50 in Cell Phones - Electronics at Digital Store, Digital-Store.com. HTC Touch Diamond signals a giant leap forward in combining hi-tech prowess with intuitive usability and exhilarating design';
                break;
            case MageWorx_SeoXTemplates_Helper_Template_Product::PRODUCT_META_KEYWORDS:
                $comment = '<p><p><b>Template variables</b><br>
                    <p>[attribute] — e.g. [name], [price], [manufacturer], [color] — will be replaced with the respective product attribute value or removed if value is not available<br>
                    <p>[attribute1|attribute2|...] — e.g. [manufacturer|brand] — if the first attribute value is not available for the product the second will be used and so on untill it finds a value<br>
                    <p>[prefix {attribute} suffix] or<br>
                    <p>[prefix {attribute1|attribute2|...} suffix] — e.g. [({color} color)] — if an attribute value is available it will be prepended with prefix and appended with suffix, either prefix or suffix can be used alone.<br>
                    <p>Randomizer feature is available. The construction like [Buy||Order||Purchase] will use a randomly picked word for each next item when applying a template.<br>
                    Also randomizers can be used within other template variables, ex: [for only||for {price}]. Number of randomizers blocks is not limited within the template.<br>
                    <p>Additional variables available for Product Meta Keywords: [category], [categories], [store_name], [website_name]<br>
                    ' . $additionalComment . '
                    <p><b>Example</b><br>
                    <p>[name][, {color} color][, {size} measurements||size][, {category}] <p>will be transformed into<br>
                    <p>CN Clogs Beach/Garden Clog, Blue color, 10 size, Shoes';
                break;
            case MageWorx_SeoXTemplates_Helper_Template_Product::PRODUCT_GALLERY:
                $comment = '<p><p><br>
                    <b>Earlier input data for product image labels will be rewriting.</b><br>
                    <p><b>Template variables</b><br>
                    <p>[attribute] — e.g. [name], [price], [manufacturer], [color] — will be replaced with the respective product attribute value or removed if value is not available<br>
                    <p>[attribute1|attribute2|...] — e.g. [manufacturer|brand] — if the first attribute value is not available for the product the second will be used and so on untill it finds a value<br>
                    <p>[prefix {attribute} suffix] or<br>
                    <p>[prefix {attribute1|attribute2|...} suffix] — e.g. [({color} color)] — if an attribute value is available it will be prepended with prefix and appended with suffix, either prefix or suffix can be used alone<br>
                    <p><b>Examples</b><br>
                    <p>[name][ - color is {color}] will be transformed (if attribute <i>color</i> exist) into<br>
                    <p>BlackBerry 8100 Pearl - color is Silver.';
                break;
            default:
                throw new Exception($this->__('SEO XTemplates: Unknow Product Template Type'));
                break;
        }

        return $comment;
    }
}