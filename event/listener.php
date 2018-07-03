<?php
/**
 * Created by PhpStorm.
 * User: Guntis
 * Date: 29.05.2018
 * Time: 12:10
 */

namespace indextree\newsletter\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 */
class listener implements EventSubscriberInterface
{
    static public function getSubscribedEvents()
    {
        return array(
            'core.user_setup'	=> 'load_language_on_setup',
            'core.page_header'	=> 'add_page_header_link',
            'core.page_header_after' => 'page_header_after',
            'core.ucp_prefs_modify_common'			=> 'modify_ucp_prefs',
            'core.ucp_prefs_personal_update_data'	=> 'update_ucp_newsletter',
        );
    }

    /* @var \phpbb\controller\helper */
    protected $helper;

    /* @var \phpbb\template\template */
    protected $template;

    /* @var \phpbb\request\request */
    protected $request;

    /* @var \phpbb\config\config */
    protected $config;

    /* @var \phpbb\user */
    protected $user;

    /** @var \phpbb\db\driver\driver */
    protected $db;

    /**
     * Constructor
     *
     * @param \phpbb\controller\helper	$helper		Controller helper object
     * @param \phpbb\template\template	$template	Template object
     * @param \phpbb\request\request	$request	Request object
     */
    public function __construct(\phpbb\controller\helper $helper,
                                \phpbb\template\template $template,
                                \phpbb\request\request $request,
                                \phpbb\config\config $config,
                                \phpbb\user $user,
                                \phpbb\db\driver\driver_interface $db)
    {
        $this->helper = $helper;
        $this->template = $template;
        $this->request = $request;
        $this->config = $config;
        $this->user = $user;
        $this->db	= $db;
    }



    public function load_language_on_setup($event)
    {
        $lang_set_ext = $event['lang_set_ext'];
        $lang_set_ext[] = array(
            'ext_name' => 'indextree/newsletter',
            'lang_set' => 'common',
        );
        $event['lang_set_ext'] = $lang_set_ext;
    }

    public function add_page_header_link($event)
    {
        $this->template->assign_vars(array(
            'U_NEWSLETTER_UNSUBSCRIBE'	=> $this->helper->route('indextree_newsletter_controller_unsubscribe', array('link' => 'world')),
            'U_NEWSLETTER_SUBSCRIBE'	=> $this->helper->route('indextree_newsletter_controller_subscribe'),
        ));
    }

    public function page_header_after($event)
    {
        $module_enable = ($this->config['indextree_newsletter_enable']) ? true : false;
        $module_name = ($this->config['indextree_newsletter_name']) ? true : false;

        $this->template->assign_vars(array(
            'S_INDEXTREE_NEWSLETTER_ENABLE' => $module_enable,
            'S_INDEXTREE_NEWSLETTER_NAME' => $module_name,
            'S_USER_SUBSCRIBED' => $this->user->data['user_it_newsletter_subscribed'],
            'S_SUBSCRIBE_POSITION' => $this->config['indextree_newsletter_position']
        ));
    }

    public function modify_ucp_prefs($event)
    {
        $this->template->assign_vars(array(
            'USER_ENABLED_NEWSLETTER'	=> $this->user->data['user_it_newsletter_subscribed'],
        ));
    }

    public function update_ucp_newsletter($event)
    {
        if ($this->request->variable('newsletter', (bool)$this->user->data['user_it_newsletter_subscribed'])) {
                $data = [
                    'subscriber_name' => $this->user->data['username'],
                    'subscriber_email' => $this->user->data['user_email'],
                    'unsubscribe_id' => $this->generateRandomToken(),
                ];
//
                $sql_insert = 'INSERT INTO phpbb_indextree_newsletter ' . $this->db->sql_build_array('INSERT', $data);
                $this->db->sql_query($sql_insert);

                $sql_select = 'SELECT subscriber_id
                    FROM phpbb_indextree_newsletter
                    WHERE subscriber_email = "' . $this->user->data['user_email'] . '"';
                $result = $this->db->sql_query($sql_select);

                while ($row = $this->db->sql_fetchrow($result)) {
                    $sql_arr = [
                        'user_it_newsletter_id' => $row['subscriber_id'],
                        'user_it_newsletter_subscribed' => true
                    ];

                    $sql_update = 'UPDATE ' . USERS_TABLE . ' SET ' .
                        $this->db->sql_build_array('UPDATE', $sql_arr) .
                        ' WHERE user_id = ' . (int)$this->user->data['user_id'];

                    $this->db->sql_query($sql_update);
                }

        } else {
            $sql_in = array($this->user->data['user_it_newsletter_id']);

            $sql = 'DELETE FROM phpbb_indextree_newsletter 
            WHERE ' . $this->db->sql_in_set('subscriber_id', $sql_in);

            $this->db->sql_query($sql);

            $event['sql_ary'] += array(
                'user_it_newsletter_subscribed' => $this->request->variable('newsletter', (bool)$this->user->data['user_it_newsletter_subscribed']),
                'user_it_newsletter_id' => 0,
            );
        }
    }

    /**
     * Generates a random token string of specified length.
     * @param int $length
     * @return string
     */
    function generateRandomToken($length = 50) {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charsLength = strlen($chars);

        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $chars[rand(0, $charsLength - 1)];
        }

        return $randomString;
    }
}