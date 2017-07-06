<?php
/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoXTemplates_Helper_Template_Comment_Blog extends MageWorx_SeoXTemplates_Helper_Template_Comment
{
    /**
     * Retrive comment for template edit page
     * @param int $typeId
     * @return string
     * @throws Exception
     */
    public function getComment($typeId)
    {
        $comment = '';
        switch($typeId){
            case MageWorx_SeoXTemplates_Helper_Template_Blog::BLOG_TITLE:
                $comment = '<p><p><b>Template variables</b><br>
                    <p>[title], [short_content], [categories], [poster], [tags], [created_time], [update_time]
                    <p><strong>Syntax:</strong>
                    <p>1) If a product attribute is missing for a given product, the result of this template will be blank
                    <p>2) [Prefix {Template_Varibale} Suffix] will return value for non-blank template variables only.
                    <p><b>Example</b><br>
                    <p>[title][ by {poster}][ on {update_time|created_time}][ in {categories}] <p>will be transformed into<br>
                    <p>7 Things that May Ruin Your International SEO Campaign by Vitaly Gonkov on 02 JULY 2015 in SEO, International SEO, Tips';
                break;
            case MageWorx_SeoXTemplates_Helper_Template_Blog::BLOG_META_DESCRIPTION:
                $comment = '<p><p><b>Template variables</b><br>
                    <p>[title], [short_content], [categories], [poster], [tags], [created_time], [update_time]
                    <p><strong>Syntax:</strong>
                    <p>1) If a product attribute is missing for a given product, the result of this template will be blank
                    <p>2) [Prefix {Template_Varibale} Suffix] will return value for non-blank template variables only.
                    <p><b>Example</b><br>
                    <p>Meta description: [short_content] [categories] <p>will be transformed into<br>
                    <p>With billions of online shoppers around the globe, it doesn’t make sense to limit your eCommerce business to just one country, region and language. SEO, International SEO, Tips';
                break;
            case MageWorx_SeoXTemplates_Helper_Template_Blog::BLOG_META_KEYWORDS:
                $comment = '<p><p><b>Template variables</b><br>
                    <p>[title], [short_content], [categories], [poster], [tags], [created_time], [update_time]
                    <p><strong>Syntax:</strong>
                    <p>1) If a product attribute is missing for a given product, the result of this template will be blank
                    <p>2) [Prefix {Template_Varibale} Suffix] will return value for non-blank template variables only.
                    <p><b>Example</b><br>
                    <p>Meta keywords: [tags] [categories] <p>will be transformed into<br>
                    <p>magento seo, multilingual seo, multiregional seo, seo';
                break;
            default:
                throw new Exception($this->__('SEO XTemplates: Unknow Blog Template Type'));
                break;
        }

        return $comment;
    }
}