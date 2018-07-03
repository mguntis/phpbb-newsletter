<?php
/**
 * Created by PhpStorm.
 * User: Guntis
 * Date: 29.05.2018
 * Time: 12:16
 */

if (!defined('IN_PHPBB'))
{
    exit;
}

if (empty($lang) || !is_array($lang))
{
    $lang = array();
}

$lang = array_merge($lang, array(
    'ACP_IT_NEWSLETTER_TITLE'         => 'Index-Tree Newsletter',
    'ACP_NEWSLETTER_DOWNLOAD'         => 'Download subscribers',
    'ACP_NEWSLETTER_CONFIG'               => 'Newsletter settings',
    'ACP_NEWSLETTER_ENABLE'       => 'Enable newsletter subscribe',
    'ACP_NEWSLETTER_SETTING_SAVED' => 'Settings have been saved successfully!',
    'ACP_NEWSLETTER_ENABLE_EXPLAIN' => 'Enable subscribe opportunity',
    'ACP_NEWSLETTER_NAME' => 'Allow insert name on subscription',
    'ACP_NEWSLETTER_NAME_EXPLAIN' => 'In subscription form allow insert name',
    'ACP_NEWSLETTER_EMAIL' => '',
    'ACP_NEWSLETTER_EMAIL_EXPLAIN' => '',
    'ACP_SETTINGS' => 'Newsletter settings',
    'ACP_SUBSCRIBERS' => 'Download subscribers',

    'ACP_DOWNLOAD_FILETYPE' => 'Download file type',
    'ACP_DOWNLOAD_FILETYPE_EXPLAIN' => 'To which format you want download the file',
    'ACP_DOWNLOAD_NAME' => 'Include name',
    'ACP_DOWNLOAD_NAME_EXPLAIN' => 'In file include name',
    'ACP_DOWNLOAD' => 'Download subscribers list',
    'ACP_DOWNLOAD_SEPERATOR' => 'Delimiter of row data',
    'ACP_DOWNLOAD_SEPERATOR_EXPLAIN' => 'Delimiter-separated values for csv and text files',
    'ACP_NEWSLETTER_POSITION' => 'Subscribe form position',
    'ACP_NEWSLETTER_POSITION_EXPLAIN' => 'In which place subscribe form need to be',

    'UCP_NEWSLETTER_TITLE' => 'workpermit.com Newsletter',
    'UCP_NEWSLETTER_EXPLAIN' => 'Produced by the workpermit.com news team you will receive regular news and updates on visa opportunities around the World',
    'UNSUBSCRIBE' => 'Unsubscribe',
    'SUBSCRIBE' => 'Subscribe',
    'TO_PAGE' => 'Go to page',

    'H2_SUBSCRIBE' => 'SUBSCRIBE TO IMMIGRATION NEWSLETTER',
    'H3_SUBSCRIBE' => 'Receive updates and latest news direct from workpermit.com team. Simply enter your email below:',
    'WARNING' => 'WARNING!',
    'INFO' => 'INFORMATION!',
    'SUCCESS' => 'SUCCESS!',
    'SUCCESS_MESSAGE_UNSUBSCRIBE' => 'You have successfully unsubscribed from our email list. We\'re sorry to see you go.',
    'SUCCESS_MESSAGE_SUBSCRIBE' => 'YOU HAVE SUCCESSFULLY SUBSCRIBED TO THE NEWSLETTER',
    'EMAIL_UNSUBSCRIBE' => 'Email to unsubscribe:',
    'LEFT_MESAGE' => 'Before you go it would really help us to improve our service to others if you could explain why you have left:',

    'ANSWER1' => 'Your newsletter is no longer relevant to me',
    'ANSWER2' => 'Your newsletter is too frequent',
    'ANSWER3' => 'I don’t remember signing up for this',
    'ANSWER4' => 'I have another reason for leaving: ',

    'CHAR_LEFT' => 'Characters left:',

    'SUBSCRIBE_POSITION_BEFORE_CONTENT' => 'Before Content',
    'SUBSCRIBE_POSITION_AFTER_CONTENT' => 'After Content',
    'NEWSLETTER_REQUIRE_540' => 'This extension requires at least PHP version 5.4.0 and phpBB version 3.2.1. Please update your PHP version and/or your phpBB version in order to use the extension.',
));

