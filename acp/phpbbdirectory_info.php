<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2025 nextgen <http://nextgen.gt>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace nextgen\phpbbdirectory\acp;

class phpbbdirectory_info
{
	public function module()
	{
		return array(
			'filename'		=> '\nextgen\phpbbdirectory\acp\phpbbdirectory_module',
			'title'			=> 'ACP_DIRECTORY',
			'modes'			=> array(
				''				=> array('title' => 'ACP_DIRECTORY',			'auth'	=> 'ext_nextgen/phpbbdirectory', 'cat' => array('')),
				'main'			=> array('title' => 'ACP_DIRECTORY_MAIN',		'auth'	=> 'ext_nextgen/phpbbdirectory', 'cat' => array('ACP_DIRECTORY')),
				'settings'		=> array('title' => 'ACP_DIRECTORY_SETTINGS',	'auth'	=> 'ext_nextgen/phpbbdirectory', 'cat' => array('ACP_DIRECTORY')),
				'cat'			=> array('title' => 'ACP_DIRECTORY_CATS',		'auth'	=> 'ext_nextgen/phpbbdirectory', 'cat' => array('ACP_DIRECTORY')),
				'val'			=> array('title' => 'ACP_DIRECTORY_VAL',		'auth'	=> 'ext_nextgen/phpbbdirectory', 'cat' => array('ACP_DIRECTORY')),
			),
		);
	}
}
