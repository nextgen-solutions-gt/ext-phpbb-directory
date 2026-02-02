<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2025 nextgen <http://nextgen.gt>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace nextgen\phpbbdirectory\controller\acp;

use \nextgen\phpbbdirectory\core\helper;

class main extends helper
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var string Custom form action */
	protected $u_action;
	
	/** @var string phpBB root path */
    protected $root_path;
	
	/** @var \phpbb\config\config */
    protected $config;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface 		$db			Database object
	* @param \phpbb\language\language				$language	Language object
	* @param \phpbb\request\request					$request	Request object
	* @param \phpbb\template\template				$template	Template object
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\language\language $language, \phpbb\request\request $request, \phpbb\template\template $template, $root_path, \phpbb\config\config $config)
	{
		$this->db			= $db;
		$this->language		= $language;
		$this->template		= $template;
		$this->request		= $request;
		$this->root_path    = $root_path;
		$this->config	    = $config;
	}

	/**
	* Display confirm box
	*
	* @param	string $action Requested action
	* @return	null
	*/
	public function display_confirm($action)
	{
		switch ($action)
		{
			case 'votes':
				$confirm = true;
				$confirm_lang = 'DIR_RESET_VOTES_CONFIRM';
				break;

			case 'comments':
				$confirm = true;
				$confirm_lang = 'DIR_RESET_COMMENTS_CONFIRM';
				break;

			case 'clicks':
				$confirm = true;
				$confirm_lang = 'DIR_RESET_CLICKS_CONFIRM';
				break;

			case 'orphans':
				$confirm = true;
				$confirm_lang = 'DIR_DELETE_ORPHANS';
				break;

			default:
				$confirm = false;
		}

		if ($confirm)
		{
			confirm_box(false, $this->language->lang($confirm_lang), build_hidden_fields(array(
				'action'	=> $action,
			)));
		}
	}

	/**
	* Display phpBB Directory statistics
	*
	* @return null
	*/
	public function display_stats()
	{
		// Count number of categories
		$sql = 'SELECT COUNT(cat_id) AS nb_cats
			FROM ' . $this->categories_table;
		$result = $this->db->sql_query($sql);
		$total_cats = (int) $this->db->sql_fetchfield('nb_cats');
		$this->db->sql_freeresult($result);

		// Cont number of links
		$sql = 'SELECT link_id, link_active
			FROM ' . $this->links_table;
		$result = $this->db->sql_query($sql);
		$total_links = $waiting_links = 0;
		while ($row = $this->db->sql_fetchrow($result))
		{
			$total_links++;

			if (!$row['link_active'])
			{
				$waiting_links++;
			}
		}
		$this->db->sql_freeresult($result);

		// Comments number calculating
		$sql = 'SELECT COUNT(comment_id) AS nb_comments
			FROM ' . $this->comments_table;
		$result = $this->db->sql_query($sql);
		$total_comments = (int) $this->db->sql_fetchfield('nb_comments');
		$this->db->sql_freeresult($result);

		// Votes number calculating
		$sql = 'SELECT COUNT(vote_id) AS nb_votes
			FROM ' . $this->votes_table;
		$result = $this->db->sql_query($sql);
		$total_votes = (int) $this->db->sql_fetchfield('nb_votes');
		$this->db->sql_freeresult($result);

		// Click number calculating
		$sql = 'SELECT SUM(link_view) AS nb_clicks
			FROM ' . $this->links_table;
		$result = $this->db->sql_query($sql);
		$total_clicks = (int) $this->db->sql_fetchfield('nb_clicks');
		$this->db->sql_freeresult($result);

		// Click number calculating
        $sql = 'SELECT SUM(link_view) AS nb_clicks
            FROM ' . $this->links_table;
        $result = $this->db->sql_query($sql);
        $total_clicks = (int) $this->db->sql_fetchfield('nb_clicks');
        $this->db->sql_freeresult($result);

        // --- CÁLCULO DE TAMAÑO DE BANNERS CORREGIDO ---
        $banners_dir_size = 0;
        $banners_path = $this->root_path . 'images/directory/banners/'; // Ruta directa
        
        // Usamos la función nativa de phpBB para listar archivos de forma segura
        $imglist = filelist($banners_path);

        if (!empty($imglist['']))
        {
            foreach ($imglist[''] as $file)
            {
                // Ignoramos archivos de sistema, protección y carpetas
                if (strpos($file, 'index.') === false && strpos($file, '.htaccess') === false)
                {
                    $full_path = $banners_path . $file;
                    if (file_exists($full_path))
                    {
                        $banners_dir_size += @filesize($full_path);
                    }
                }
            }
            // get_formatted_filesize es una función nativa de phpBB
            $banners_dir_size = get_formatted_filesize($banners_dir_size);
        }
        else
        {
            $banners_dir_size = $this->language->lang('NOT_AVAILABLE');
        }

        $total_orphan = $this->_orphan_files();
		
		// 1. Obtener versión local desde composer.json
		$composer_path = $this->root_path . 'ext/nextgen/phpbbdirectory/composer.json';
		$current_version = '0.0.0';

		if (file_exists($composer_path))
		{
			$composer_data = json_decode(file_get_contents($composer_path), true);
			$current_version = (isset($composer_data['version'])) ? $composer_data['version'] : '0.0.0';
		}

		// 2. Leer versión remota desde GitHub
		$remote_url = 'https://raw.githubusercontent.com/nextgen-solutions-gt/ext-phpbb-directory/3.3/phpbbdirectory_versions.json';
		$latest_version = $current_version; 
		$download_url = '';

		$remote_file = @file_get_contents($remote_url);
		if ($remote_file)
		{
			$versions_data = json_decode($remote_file, true);
			// Accedemos a la rama 3.3 de unstable según tu JSON
			if (isset($versions_data['unstable']['3.3']['current']))
			{
				$latest_version = $versions_data['unstable']['3.3']['current'];
				$download_url = $versions_data['unstable']['3.3']['download'];
			}
		}

		// 3. Cálculo de la comparación (IMPORTANTE: Definir la variable aquí)
		$up_to_date = version_compare($current_version, $latest_version, '>=');
		
		$this->template->assign_vars(array(
			'U_ACTION'			=> $this->u_action,

			'TOTAL_CATS'		=> $total_cats,
			'TOTAL_LINKS'		=> $total_links-$waiting_links,
			'WAITING_LINKS'		=> $waiting_links,
			'TOTAL_COMMENTS'	=> $total_comments,
			'TOTAL_VOTES'		=> $total_votes,
			'TOTAL_CLICKS'		=> $total_clicks,
			'TOTAL_ORPHANS'		=> $total_orphan,
			'BANNERS_DIR_SIZE'	=> $banners_dir_size,
			'U_ACTION'          => $this->u_action,
			'CURRENT_VERSION'   => $current_version,
			'LATEST_VERSION'    => $latest_version,
			'U_DOWNLOAD_LATEST' => $download_url,
			'S_UP_TO_DATE'      => $up_to_date,
		));
	}

	/**
	* Execute action requested
	*
	* @param	string $action Requested action
	* @return	null
	*/
	public function exec_action($action)
	{
		switch ($action)
		{
			case 'votes':
				$this->db->sql_query('DELETE FROM ' . $this->votes_table);

				$sql = 'UPDATE ' . $this->links_table . '
					SET link_vote = 0, link_note = 0';
				$this->db->sql_query($sql);

				if ($this->request->is_ajax())
				{
					trigger_error('DIR_RESET_VOTES_SUCCESS');
				}
			break;

			case 'comments':
                // Clean the comments table (Standard SQL for all DB engines)
                $this->db->sql_query('DELETE FROM ' . $this->comments_table);

                $sql = 'UPDATE ' . $this->links_table . '
                    SET link_comment = 0';
                $this->db->sql_query($sql);

                if ($this->request->is_ajax())
                {
                    trigger_error('DIR_RESET_COMMENTS_SUCCESS');
                }
            break;

				$this->db->sql_query($sql);

				if ($this->request->is_ajax())
				{
					trigger_error('DIR_RESET_COMMENTS_SUCCESS');
				}

				break;

			case 'clicks':
				$sql = 'UPDATE ' . $this->links_table . '
					SET link_view = 0';
				$this->db->sql_query($sql);

				if ($this->request->is_ajax())
				{
					trigger_error('DIR_RESET_CLICKS_SUCCESS');
				}
			break;

			case 'orphans':
				$this->_orphan_files(true);

				if ($this->request->is_ajax())
				{
					trigger_error('DIR_DELETE_ORPHANS_SUCCESS');
				}
			break;
		}
	}

	/**
	* Set page url
	*
	* @param	string $u_action Custom form action
	* @return	null
	* @access	public
	*/
	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;
	}

/**
* Get and clean orphan banners
*
* @param    bool        $delete    True if we want to delete banners, else false
* @return   int|null    Number of orphan files
*/
private function _orphan_files($delete = false)
{
    // USAR LA RUTA FÍSICA REAL
    $banner_path = $this->root_path . 'images/directory/banners/';
    $imglist = filelist($banner_path);
    $physical_files = $logical_files = $orphan_files = array();

    if (!empty($imglist['']))
    {
        $physical_files = array_values($imglist['']);

        $sql = 'SELECT link_banner FROM ' . $this->links_table . "
                WHERE link_banner <> ''";
        $result = $this->db->sql_query($sql);

        while ($row = $this->db->sql_fetchrow($result))
        {
            // Limpiamos el nombre para comparar solo el nombre del archivo
            $logical_files[] = basename($row['link_banner']);
        }
        $this->db->sql_freeresult($result);

        // Comparamos arrays
        $orphan_files = array_diff($physical_files, $logical_files);
        
        // Excluimos archivos protegidos
        $protected = array('index.htm', 'index.html', '.htaccess');
        $orphan_files = array_diff($orphan_files, $protected);
    }

    if (!$delete)
    {
        return sizeof($orphan_files);
    }

    $deleted_count = 0;
    foreach ($orphan_files as $file)
    {
        // Usamos la ruta completa para borrar
        if (@unlink($banner_path . $file))
        {
            $deleted_count++;
        }
    }

    return $deleted_count;
}
}
