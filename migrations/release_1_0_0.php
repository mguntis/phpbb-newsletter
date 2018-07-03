<?php
/**
*
* @package phpBB Extension - Acme Demo
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace indextree\newsletter\migrations;

class release_1_0_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['indextree_newsletter_enable']);
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\alpha2');
	}

	public function update_data()
	{
		return array(
			array('config.add', array('indextree_newsletter_enable', 0)),
            array('config.add', array('indextree_newsletter_name', 0)),

			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_IT_NEWSLETTER_TITLE'
			)),
			array('module.add', array(
				'acp',
				'ACP_IT_NEWSLETTER_TITLE',
				array(
					'module_basename'	=> '\indextree\newsletter\acp\main_module',
					'modes'				=> array('settings', 'download'),
				),
			)),
		);
	}

}
