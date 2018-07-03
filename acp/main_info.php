<?php
/**
 * Created by PhpStorm.
 * User: Guntis
 * Date: 29.05.2018
 * Time: 12:43
 */

namespace indextree\newsletter\acp;

class main_info
{
    function module()
    {
        return array(
            'filename'	=> '\indextree\newsletter\acp\main_module',
            'title'		=> 'ACP_IT_NEWSLETTER_TITLE',
            'modes'		=> array(
                'settings'	=> array(
                    'title'	=> 'ACP_NEWSLETTER_CONFIG',
                    'auth'	=> 'ext_indextree/newsletter && acl_a_board',
                    'cat'	=> array('ACP_IT_NEWSLETTER_TITLE')
                ),
                'download'	=> array(
                    'title'	=> 'ACP_NEWSLETTER_DOWNLOAD',
                    'auth'	=> 'ext_indextree/newsletter && acl_a_board',
                    'cat'	=> array('ACP_IT_NEWSLETTER_TITLE')
                ),
            ),
        );
    }
}
