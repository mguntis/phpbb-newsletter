<?php
/**
 * Created by PhpStorm.
 * User: Guntis
 * Date: 29.05.2018
 * Time: 12:33
 */

namespace indextree\newsletter\controller;

use galandas\moods\migrations\user_data;
use phpbb\exception\http_exception;

class main
{
    /* @var \phpbb\config\config */
    protected $config;

    /* @var \phpbb\controller\helper */
    protected $helper;

    /* @var \phpbb\template\template */
    protected $template;

    /* @var \phpbb\user */
    protected $user;

    /* @var \phpbb\request\request */
    protected $request;

    /** @var \phpbb\db\driver\driver */
    protected $db;

    /**
     * Constructor
     *
     * @param \phpbb\config\config		$config
     * @param \phpbb\controller\helper	$helper
     * @param \phpbb\template\template	$template
     * @param \phpbb\user				$user
     */
    public function __construct(\phpbb\config\config $config,
                                \phpbb\controller\helper $helper,
                                \phpbb\template\template $template,
                                \phpbb\user $user,
                                \phpbb\request\request $request,
                                \phpbb\db\driver\driver_interface $db)
    {
        $this->config = $config;
        $this->helper = $helper;
        $this->template = $template;
        $this->user = $user;
        $this->request	= $request;
        $this->db	= $db;
    }

    /**
     * Demo controller for route /demo/{name}
     *
     * @param string		$link
     * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
     */
    public function handle($link)
    {
        $answer = [
          'ans1' => 'Your newsletter is no longer relevant to me',
          'ans2' => 'Your newsletter is too frequent',
          'ans3' => 'I don’t remember signing up for this',
        ];

        if ($this->config['indextree_newsletter_enable']) {
            $sql_select = 'SELECT subscriber_email
                    FROM phpbb_indextree_newsletter
                    WHERE unsubscribe_id = "' . $link . '"';
            $result = $this->db->sql_query($sql_select);

            $email = null;
            while ($row = $this->db->sql_fetchrow($result)) {
                $email = $row['subscriber_email'];
            }

            if (empty($email)) {
                trigger_error('No route found for "GET /newsletter/unsubscribe/' . $link . '"');
            }

            if ($this->request->is_set_post('submit')) {

                $answer_find = $this->request->variable('answer-other', '');
                $answer_req = (empty($answer_find)) ? $answer[$this->request->variable('answer', '')] : $answer_req = $answer_find;

                if (empty($answer_req)) {
                    $this->template->assign_vars(array(
                        'ERROR' => "Please select one option!",
                        'EMAIL' => $email,
                        'UNSUBSCRIBE_PAGE' => true,
                    ));
                } else {
                    if(strlen($answer_req) > 45){
                        $this->template->assign_vars(array(
                            'ERROR' => "Your answer needs to be a bit shorter.",
                            'EMAIL' => $email,
                            'UNSUBSCRIBE_PAGE' => true,
                        ));
                    } else {
                        $data = [
                            'subscriber_email' => $email,
                            'unsubscribe_reason' => $answer_req,
                        ];
//
                        $sql_insert = 'INSERT INTO phpbb_indextree_unsubscribe_review ' . $this->db->sql_build_array('INSERT', $data);
                        $this->db->sql_query($sql_insert);

                        $sql_select = 'SELECT user_id
                    FROM ' . USERS_TABLE . '
                    WHERE user_email = "' . $email . '"';
                        $result = $this->db->sql_query($sql_select);

                        $user_id = null;
                        while ($row = $this->db->sql_fetchrow($result)) {
                            $user_id = $row['user_id'];
                        }

                        if (!empty($user_id)) {
                            $sql_arr = [
                                'user_it_newsletter_id' => 0,
                                'user_it_newsletter_subscribed' => false
                            ];

                            $sql_update = 'UPDATE ' . USERS_TABLE . ' SET ' .
                                $this->db->sql_build_array('UPDATE', $sql_arr) .
                                ' WHERE user_id = ' . $user_id;

                            $this->db->sql_query($sql_update);
                        }

                        $sql_in = array($email);

                        $sql = 'DELETE FROM phpbb_indextree_newsletter WHERE ' . $this->db->sql_in_set('subscriber_email', $sql_in);

                        $this->db->sql_query($sql);

                        $this->template->assign_vars(array(
//                            'ERROR' => "Please select one option!"."<pre>".print_r($answer_req,true)."</pre>",
                            'UNSUBSCRIBE_PAGE' => False,
                        ));
                    }
                }

            } else {

                $this->template->assign_vars(array(
                    'EMAIL' => $email,
                    'UNSUBSCRIBE_PAGE' => True,
                ));
            }

            return $this->helper->render('unsubscribe_body.html', $link);
        } else {
            trigger_error('No route found for "GET /'.$link.'"');
        }
    }

    /**
     * Demo controller for route /demo/{name}
     *
     * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
     */
    public function subscribe()
    {
        if (!$this->config['indextree_newsletter_enable']) {
            return trigger_error('No route found for "GET /newsletter/subscribe"');
        }

        if ($this->request->is_set_post('submit'))
        {
            if ($this->user->data['user_id'] == ANONYMOUS) {

                $name = $this->request->variable("name", 'subscriber');
                $email = $this->request->variable("mail", '');

                if ((empty($name) || empty($email)) && $this->config['indextree_newsletter_name']){
                    trigger_error('Email and name can not be blank');
                }

                if (!preg_match("/^[a-zA-Z ]*$/", $name) && $this->config['indextree_newsletter_name']) {
                    trigger_error("Only letters and white space allowed");
                }

                if ((strlen($name) > 45 || strlen($email) > 254) && $this->config['indextree_newsletter_name']) {
                    trigger_error("Name or email are too long");
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    trigger_error("Invalid email format");
                }

                $sql = 'SELECT subscriber_id, subscriber_email
                FROM phpbb_indextree_newsletter
                WHERE subscriber_email = "'.$email.'"';
                $result = $this->db->sql_query($sql);

                $row_data = [];
                while ($row = $this->db->sql_fetchrow($result))
                {
                    $row_data[$row['subscriber_id']] = $row['subscriber_email'];
                }

                if (!empty($row_data) || count($row_data) > 1) {
                    return trigger_error("Email has already registered");
                }

                $data = array(
                    'subscriber_name'     => $name,
                    'subscriber_email'     => strtolower($email),
                    'unsubscribe_id' => $this->generateRandomToken(),
                );

                $sql = 'INSERT INTO phpbb_indextree_newsletter ' . $this->db->sql_build_array('INSERT', $data);
                $this->db->sql_query($sql);

                $sql_subs_id = 'SELECT subscriber_id
                FROM phpbb_indextree_newsletter
                WHERE subscriber_email = "'.$email.'"';
                $result = $this->db->sql_query($sql_subs_id);

                $subscriber_id = 0;
                while ($row = $this->db->sql_fetchrow($result))
                {
                    $subscriber_id = $row['subscriber_id'];
                }

                $sql_select = 'SELECT user_id
                FROM ' . USERS_TABLE . '
                WHERE user_email = "' . $email . '"';
                $result = $this->db->sql_query($sql_select);

                $user_id = null;
                while ($row = $this->db->sql_fetchrow($result)) {
                    $user_id = $row['user_id'];
                }

                if (!empty($user_id)) {
                    $sql_arr = [
                        'user_it_newsletter_id' => $subscriber_id,
                        'user_it_newsletter_subscribed' => true
                    ];

                    $sql_update = 'UPDATE ' . USERS_TABLE . ' SET ' .
                        $this->db->sql_build_array('UPDATE', $sql_arr) .
                        ' WHERE user_id = ' . $user_id;

                    $this->db->sql_query($sql_update);
                }

                $this->template->assign_vars([
                    'PRINT' => "<pre>" . print_r($this->request, true) . "</pre>",

                ]);

            } else {
                if(!$this->user->data['user_it_newsletter_subscribed']) {
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

                    $this->template->assign_vars([
                        'PRINT' => "<pre>" . print_r($data, true) . "</pre>",
//                    'PRINT2' => "<pre>" . print_r($this->request, true) . "</pre>",
                        'S_IT_SUBSCRIBED' => true,

                    ]);
                } else {
                    return trigger_error("You already subscribed");
                }
            }

            return $this->helper->render('thanks_body.html');

        } else {
            return trigger_error('No route found for "GET /newsletter/subscribe"');
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
