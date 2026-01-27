<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2026 nextgen <https://nextgen.gt>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace nextgen\phpbbdirectory\migrations\v20x;

// El nombre de la clase DEBE ser igual al nombre del archivo
class v200_rc4_color_update extends \phpbb\db\migration\migration
{
	/**
	* Depende de la última migración exitosa que tuviste
	*/
	static public function depends_on()
	{
		return array('\nextgen\phpbbdirectory\migrations\v20x\m5_banner_optimization');
	}

	public function update_schema()
	{
		return array(
			'add_columns' => array(
				$this->table_prefix . 'directory_cats' => array(
					// Usamos VCHAR:7 para asegurar que el tipo de dato vaya antes del DEFAULT
					'cat_icon_color' => array('VCHAR:7', '#336699'),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns' => array(
				$this->table_prefix . 'directory_cats' => array(
					'cat_icon_color',
				),
			),
		);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('nextgen_dir_color_updated', '1')),
		);
	}
}