<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Observer_Redirector
{
    public function redirectHomePage(Varien_Event_Observer $observer)
    {
        $front   = $observer->getEvent()->getFront();
        $origUri = $front->getRequest()->getRequestUri();
        $origUri = explode('?', $origUri, 2);

        /** @todo 'home' - is default identifier for home page. Add compatibility with another identifiers by setting.  **/

        $uri     = preg_replace('~(?:index\.php/+home/*|index\.php/*|(/)+home/*)$~i', '', $origUri[0]);
        //if ($uri=='/') return ; // fix Vladimir Z.
        if (strpos($origUri[0], '/downloader/index.php') !== false) {
            return;
        }
        if ($uri == $origUri[0]) {
            return;
        }
        $uri = rtrim($uri, '/') . '/';
        $uri .= ( (isset($origUri[1]) && $origUri[1] !== "___SID=U") ? '?' . $origUri[1] : '');
        $front->getResponse()
                ->setRedirect($uri)
                ->setHttpResponseCode(301)
                ->sendResponse();
        exit;
    }
}