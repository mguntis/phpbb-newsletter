<?php
/**
 * Created by PhpStorm.
 * User: Guntis
 * Date: 29.05.2018
 * Time: 12:44
 */

namespace indextree\newsletter\acp;

class main_module
{
    var $u_action;

    function main($id, $mode)
    {
        global $config, $request, $template, $user, $db;

        switch ($mode) {
            case 'settings':
                $user->add_lang('acp/common');
                $this->tpl_name = 'settings_body';
                $this->page_title = $user->lang('ACP_IT_NEWSLETTER_TITLE');
                add_form_key('indextree/newsletter');

                if ($request->is_set_post('submit'))
                {
                    if (!check_form_key('indextree/newsletter'))
                    {
                        trigger_error('FORM_INVALID');
                    }

                    $config->set('indextree_newsletter_enable', $request->variable('indextree_newsletter_enable', 0));
                    $config->set('indextree_newsletter_name', $request->variable('indextree_newsletter_name', 0));
                    $config->set('indextree_newsletter_position', $request->variable('indextree_newsletter_position', 'Before Content'));

                    //"<pre>".print_r($request,true)."</pre>"
                    trigger_error($user->lang('ACP_NEWSLETTER_SETTING_SAVED'). adm_back_link($this->u_action));
                }

                $newsletter_subscribe_position = '';

                $subscribe_constants = [
                    'BEFORE_CONTENT' => 'Before Content',
                    'AFTER_CONTENT' => 'After Content',
                ];

                foreach ($subscribe_constants as $name => $value) {
                    $selected = ($value == $config['indextree_newsletter_position']) ? ' selected="selected"' : '';
                    $position_name = $user->lang('SUBSCRIBE_POSITION_' . $name);
                    $newsletter_subscribe_position  .= "<option value='{$value}'$selected>$position_name</option>";
                }


                $template->assign_vars(array(
                    'U_ACTION'				=> $this->u_action,
                    'INDEXTREE_NEWSLETTER_ENABLE'		=> $config['indextree_newsletter_enable'],
                    'INDEXTREE_NEWSLETTER_NAME'		=> $config['indextree_newsletter_name'],
                    'INDEXTREE_NEWSLETTER_POSITION'		=> $newsletter_subscribe_position,
                ));
                break;

            case 'download':
                $user->add_lang('acp/common');
                $this->tpl_name = 'subscribers_body';
                $this->page_title = $user->lang('ACP_NEWSLETTER_DOWNLOAD');
                add_form_key('indextree/newsletter');

                if ($request->is_set_post('submit'))
                {
                    if (!check_form_key('indextree/newsletter'))
                    {
                        trigger_error('FORM_INVALID');
                    }

                    $config->set('indextree_download_name', $request->variable('indextree_download_name', 0));
                    $config->set('indextree_download_seperator', $request->variable('indextree_download_seperator', ','));

                    $file_type = $request->variable('indextree_download_filetype', 0);

                    $file_types = [
                        1 => '.csv',
                        2 => '.txt',
                        3 => '.json'
                    ];

                    $data = [];

                    if ($config['indextree_download_name']) {

                        $select_string = 'subscriber_name, subscriber_email, unsubscribe_id';

                        $sql_select = 'SELECT '.$select_string.' FROM phpbb_indextree_newsletter';
                        $result = $db->sql_query($sql_select);

                        $data[] = [
                            'subscriber_name' => 'Name',
                            'subscriber_email' => 'E-Mail',
                            'unsubscribe_id' => 'Code'
                        ];

                        while ($row = $db->sql_fetchrow($result)) {
                            $data[] = [
                                'subscriber_name' => $row['subscriber_name'],
                                'subscriber_email' => strtolower($row['subscriber_email']),
                                'unsubscribe_id' => $row['unsubscribe_id']
                            ];

                        }

                    } else {
                        $select_string = 'subscriber_email, unsubscribe_id';

                        $sql_select = 'SELECT '.$select_string.' FROM phpbb_indextree_newsletter';
                        $result = $db->sql_query($sql_select);

                        $data[] = [
                            'subscriber_email' => 'E-Mail',
                            'unsubscribe_id' => 'Code'
                        ];

                        while ($row = $db->sql_fetchrow($result)) {
                            $data[] = [
                                'subscriber_email' => $row['subscriber_email'],
                                'unsubscribe_id' => $row['unsubscribe_id']
                            ];
                        }
                    }

                    $file_name = 'subscribers'.$file_types[$file_type];

                    $fp = fopen($file_name,'w');

                    if($file_types[$file_type] == '.json') {
                        fwrite($fp, json_encode($data));
                    } else {
                        foreach ($data as $line) {
                            fputcsv($fp, $line, $config['indextree_download_seperator']);
                        }
                    }

                    fclose($fp);

                    // get the file mime type using the file extension
                    switch(strtolower(substr(strrchr($file_name, '.'), 1))) {
                        case 'csv': $mime = 'text/plain'; break;
                        case 'txt': $mime = 'text/csv'; break;
                        case 'json': $mime = 'application/json'; break;
                        default: $mime = 'text/plain';
                    }

                    header('Pragma: public'); 	// required
                    header('Expires: 0');		// no cache
                    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                    header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($file_name)).' GMT');
                    header('Cache-Control: private',false);
                    header('Content-Type: '.$mime);
                    header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
                    header('Content-Transfer-Encoding: binary');
                    header('Content-Length: '.filesize($file_name));	// provide file size
                    header('Connection: close');
                    readfile($file_name);		// push it out
                    exit();
                }


                $template->assign_vars(array(
                    'U_ACTION'				=> $this->u_action,
                    'INDEXTREE_DOWNLOAD_NAME'		=> $config['indextree_download_name'],
                    'INDEXTREE_DOWNLOAD_SEPERATOR'		=> $config['indextree_download_seperator'],
                ));

                break;
        }
    }
}
