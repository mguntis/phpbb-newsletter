<?php
/**
 * Created by PhpStorm.
 * User: Guntis
 * Date: 29.05.2018
 * Time: 12:37
 */

namespace indextree\newsletter;

/**
 * @ignore
 */

/**
 * Class ext
 *
 * It is recommended to remove this file from
 * an extension if it is not going to be used.
 */
class ext extends \phpbb\extension\base
{
    /**
     * Enable extension if phpBB version requirement is met
     *
     * @return bool
     * @access public
     */
    public function is_enableable()
    {
        $config = $this->container->get('config');
        $enableable = (phpbb_version_compare($config['version'], '3.2.1', '>=') && version_compare(PHP_VERSION, '5.4.*', '>'));

        if (!$enableable)
        {
            $user = $this->container->get('user');
            $user->add_lang_ext('indextree/newsletter', 'common');
            trigger_error($user->lang('NEWSLETTER_REQUIRE_540'), E_USER_WARNING);
        }

        return $enableable;
    }

}
