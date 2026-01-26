<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2025 nextgen <http://nextgen.gt>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace nextgen\phpbbdirectory\migrations\converter;

/**
* Convert module
*/
class convert_module extends \phpbb\db\migration\migration
{
	/**
	* Skip this migration if an ACP_DIRECTORY module does not exist
	*
	* @return	bool	True if table does not exist
	* @access	public
	*/
	public function effectively_installed()
	{
		$sql = 'SELECT module_id
			FROM ' . $this->table_prefix . "modules
			WHERE module_class = 'acp'
				AND module_basename = 'acp_directory'
				AND module_mode = 'main'";
		$result = $this->db->sql_query($sql);
		$module_id = (int) $this->db->sql_fetchfield('module_id');
		$this->db->sql_freeresult($result);

		// Skip migration if ACP_DIRECTORY module does not exist
		return !$module_id;
	}

	/**
	* Add or update data in the database
	*
	* @return	array Array of table data
	* @access	public
	*/
	public function update_data()
	{
		return array(
			array('custom', array(array(&$this, 'rename_old_module'))),
		);
	}

	public function rename_old_module()
	{
		$module_data = array(
			'module_basename'	=> '\nextgen\phpbbdirectory\acp\phpbbdirectory_module',
			'module_auth'		=> 'ext_nextgen/phpbbdirectory'
		);

		$sql = 'UPDATE '  . $this->table_prefix . 'modules
			SET ' . $this->db->sql_build_array('UPDATE', $module_data) . "
			WHERE module_basename = 'acp_directory'
				AND " . $this->db->sql_in_set('module_mode', array('main', 'settings', 'cat', 'val'));
		$this->db->sql_query($sql);
	}
}
