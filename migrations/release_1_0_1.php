<?php
/**
*
* @package phpBB Extension - Acme Demo
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace indextree\newsletter\migrations;

class release_1_0_1 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'users', 'user_indextree');
	}

	static public function depends_on()
	{
		return array('\indextree\newsletter\migrations\release_1_0_0');
	}

    public function update_schema()
    {
        return array(
            'add_tables'		=> array(
                $this->table_prefix . 'indextree_newsletter'	=> array(
                    'COLUMNS'		=> array(
                        'subscriber_id'		=> array('UINT', null, 'auto_increment'),
                        'subscriber_name'	=> array('VCHAR:255', ''),
                        'subscriber_email'	=> array('VCHAR:255', ''),
                        'unsubscribe_id' => array('VCHAR:255', ''),
                    ),
                    'PRIMARY_KEY'	=> 'subscriber_id',
                ),
                $this->table_prefix . 'indextree_unsubscribe_review'	=> array(
                    'COLUMNS'		=> array(
                        'review_id'		=> array('UINT', null, 'auto_increment'),
                        'subscriber_email'	=> array('VCHAR:255', ''),
                        'unsubscribe_reason' => array('VCHAR:255', ''),
                    ),
                    'PRIMARY_KEY'	=> 'review_id',
                ),
            ),
            'add_columns'	=> array(
                $this->table_prefix . 'users'			=> array(
                    'user_it_newsletter_id'				=> array('UINT', 0),
                    'user_it_newsletter_subscribed'		=> array('BOOL', 0),
                ),
            ),
        );
    }

	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'users' => array(
                    'user_it_newsletter_id',
                    'user_it_newsletter_subscribed',
				),
			),
			'drop_tables' => array(
				$this->table_prefix . 'indextree_newsletter',
                $this->table_prefix. 'indextree_unsubscribe_review'
			),
		);
	}
}
