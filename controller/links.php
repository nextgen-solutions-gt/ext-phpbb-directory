<?php
/**
*
* phpBB Directory extension for the phpBB Forum Software package.
*
* @copyright (c) 2025 nextgen <http://nextgen.gt>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace nextgen\phpbbdirectory\controller;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use \nextgen\phpbbdirectory\core\helper;

class links extends helper
{
    private $link_user_id;
    private $site_name;
    private $url;
    private $description;
    private $guest_email;
    private $rss;
    private $banner;
    private $back;
    private $flag;

    private $captcha;
    private $s_hidden_fields = array();

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\captcha\factory */
	protected $captcha_factory;

	/** @var \nextgen\phpbbdirectory\core\categorie */
	protected $categorie;

	/** @var \nextgen\phpbbdirectory\core\link */
	protected $link;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string phpEx */
	protected $php_ext;

    /** @var \phpbb\files\factory */
    protected $files_factory;

    /**
    * Constructor
    *
    * @param \phpbb\db\driver\driver_interface         $db                     Database object
    * @param \phpbb\config\config                     $config                 Config object
    * @param \phpbb\language\language                 $language               Language object
    * @param \phpbb\template\template                 $template               Template object
    * @param \phpbb\user                              $user                   User object
    * @param \phpbb\controller\helper                 $helper                 Controller helper object
    * @param \phpbb\request\request                   $request                Request object
    * @param \phpbb\auth\auth                         $auth                   Auth object
    * @param \phpbb\captcha\factory                   $captcha_factory        Captcha object
    * @param \nextgen\phpbbdirectory\core\categorie   $categorie              PhpBB Directory extension categorie object
    * @param \nextgen\phpbbdirectory\core\link        $link                   PhpBB Directory extension link object
    * @param string                                   $root_path              phpBB root path
    * @param string                                   $php_ext                phpEx
    * @param \phpbb\files\factory                     $files_factory          Files factory object
    */
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\config\config $config, \phpbb\language\language $language, \phpbb\template\template $template, \phpbb\user $user, \phpbb\controller\helper $helper, \phpbb\request\request $request, \phpbb\auth\auth $auth, \phpbb\captcha\factory $captcha_factory, \nextgen\phpbbdirectory\core\categorie $categorie, \nextgen\phpbbdirectory\core\link $link, $root_path, $php_ext, \phpbb\files\factory $files_factory)
    {
        $this->db               = $db;
        $this->config           = $config;
        $this->language         = $language;
        $this->template         = $template;
        $this->user             = $user;
        $this->helper           = $helper;
        $this->request          = $request;
        $this->auth             = $auth;
        $this->captcha_factory  = $captcha_factory;
        $this->categorie        = $categorie;
        $this->link             = $link;
        $this->root_path        = $root_path;
        $this->php_ext          = $php_ext;
        $this->files_factory    = $files_factory;

        $template->assign_vars(array(
            'S_PHPBB_DIRECTORY'    => true,
        ));
    }

    /**
    * Delete a link
    *
    * @param    int    $cat_id        The category ID
    * @param    int    $link_id        The link ID
    * @return    null|\Symfony\Component\HttpFoundation\Response    A Symfony Response object
    */
    public function delete_link($cat_id, $link_id)
    {
        if ($this->request->is_set_post('cancel'))
        {
            $redirect = $this->helper->route('nextgen_phpbbdirectory_dynamic_route_' . $cat_id);
            redirect($redirect);
        }

        $sql = 'SELECT link_user_id
            FROM ' . $this->links_table . '
            WHERE link_id = ' . (int) $link_id;
        $result = $this->db->sql_query($sql);
        $link_data = $this->db->sql_fetchrow($result);

        if (empty($link_data))
        {
            throw new \phpbb\exception\http_exception(404, 'DIR_ERROR_NO_LINKS');
        }

        $delete_allowed = $this->user->data['is_registered'] && ($this->auth->acl_get('m_delete_dir') || ($this->user->data['user_id'] == $link_data['link_user_id'] && $this->auth->acl_get('u_delete_dir')));

        if (!$delete_allowed)
        {
            throw new \phpbb\exception\http_exception(403, 'DIR_ERROR_NOT_AUTH');
        }

        if (confirm_box(true))
        {
            $this->link->del($cat_id, $link_id);

            $meta_info = $this->helper->route('nextgen_phpbbdirectory_dynamic_route_' . $cat_id);
            meta_refresh(3, $meta_info);
            $message = $this->language->lang('DIR_DELETE_OK') . '<br /><br />' . $this->language->lang('DIR_CLICK_RETURN_DIR', '<a href="' . $this->helper->route('nextgen_phpbbdirectory_base_controller') . '">', '</a>') . '<br /><br />' . $this->language->lang('DIR_CLICK_RETURN_CAT', '<a href="' . $this->helper->route('nextgen_phpbbdirectory_dynamic_route_' . $cat_id) . '">', '</a>');
            return $this->helper->message($message);
        }
        else
        {
            confirm_box(false, 'DIR_DELETE_SITE');
        }
    }

    /**
    * Edit a link
    *
    * @param    int    $cat_id        The category ID
    * @param    int    $link_id    The link ID
    * @return    null|\Symfony\Component\HttpFoundation\Response    A Symfony Response object
    * @throws    \phpbb\exception\http_exception
    */
    public function edit_link($cat_id, $link_id)
    {
        $sql = 'SELECT link_id, link_uid, link_user_id, link_flags, link_bitfield, link_cat, link_url, link_description, link_guest_email, link_name, link_rss, link_back, link_banner, link_flag, link_cat, link_time
            FROM ' . $this->links_table . '
            WHERE link_id = ' . (int) $link_id;
        $result = $this->db->sql_query($sql);
        $link_data = $this->db->sql_fetchrow($result);
        $this->link_user_id = (int) $link_data['link_user_id'];

        if (empty($link_data['link_id']))
        {
            throw new \phpbb\exception\http_exception(404, 'DIR_ERROR_NO_LINKS');
        }

        $edit_allowed = ($this->user->data['is_registered'] && ($this->auth->acl_get('m_edit_dir') || ($this->user->data['user_id'] == (int) $link_data['link_user_id'] && $this->auth->acl_get('u_edit_dir'))));

        if (!$edit_allowed)
        {
            throw new \phpbb\exception\http_exception(403, 'DIR_ERROR_NOT_AUTH');
        }

        $cat_id        = $this->request->variable('id', $cat_id);
        $submit        = $this->request->is_set_post('submit') ? true : false;
        $refresh    = $this->request->is_set_post('refresh_vc') ? true : false;
        $title        = $this->language->lang('DIR_EDIT_SITE');

        $this->template->assign_block_vars('dir_navlinks', array(
            'FORUM_NAME'    => $title,
            'U_VIEW_FORUM'    => $this->helper->route('nextgen_phpbbdirectory_edit_controller', array('cat_id' => (int) $cat_id, 'link_id' => $link_id))
        ));

        $this->categorie->get($cat_id);

        // If form is done
        if ($submit || $refresh)
        {
            if (false != ($result = $this->_data_processing($cat_id, $link_id, 'edit')))
            {
                return $result;
            }
        }
        else
        {
            $this->s_hidden_fields = array(
                'old_cat_id'    => $link_data['link_cat'],
                'old_banner'    => $link_data['link_banner'],
            );

            $site_description            = generate_text_for_edit($link_data['link_description'], $link_data['link_uid'], $link_data['link_flags']);
            // Permitimos URL completa o nombre de archivo, no filtramos regex estricto aquí para permitir archivos locales
            $link_data['link_banner']     = $link_data['link_banner'];

            $this->url            = $link_data['link_url'];
            $this->site_name    = $link_data['link_name'];
            $this->description    = $site_description['text'];
            $this->guest_email    = $link_data['link_guest_email'];
            $this->rss            = $link_data['link_rss'];
            $this->banner         = $link_data['link_banner'];
            $this->back            = $link_data['link_back'];
            $this->flag         = $link_data['link_flag'];
        }

        $this->_populate_form($cat_id, 'edit', $title);

        return $this->helper->render('add_site.html', $title);
    }

    /**
    * Display add form
    *
    * @param    int    $cat_id        The category ID
    * @return    \Symfony\Component\HttpFoundation\Response    A Symfony Response object
    * @throws    \phpbb\exception\http_exception
    */
    public function new_link($cat_id)
    {
        if (!$this->auth->acl_get('u_submit_dir'))
        {
            throw new \phpbb\exception\http_exception(403, 'DIR_ERROR_NOT_AUTH');
        }

        $cat_id        = $this->request->variable('id', $cat_id);
        $submit        = $this->request->is_set_post('submit') ? true : false;
        $refresh    = $this->request->is_set_post('refresh_vc') ? true : false;
        $title        = $this->language->lang('DIR_NEW_SITE');

        $this->template->assign_block_vars('dir_navlinks', array(
            'FORUM_NAME'    => $title,
            'U_VIEW_FORUM'    => $this->helper->route('nextgen_phpbbdirectory_new_controller', array('cat_id' => (int) $cat_id))
        ));

        $this->categorie->get($cat_id);

        // The CAPTCHA kicks in here. We can't help that the information gets lost on language change.
        if (!$this->user->data['is_registered'] && $this->config['dir_visual_confirm'])
        {
            $this->captcha = $this->captcha_factory->get_instance($this->config['captcha_plugin']);
            $this->captcha->init(CONFIRM_POST);
        }

        // If form is done
        if ($submit || $refresh)
        {
            if (false != ($result = $this->_data_processing($cat_id)))
            {
                return $result;
            }
        }

        $this->_populate_form($cat_id, 'new', $title);

        return $this->helper->render('add_site.html', $title);
    }

    /**
    * View link controller
    *
    * @param    int    $link_id        The link ID
    * @return    \Symfony\Component\HttpFoundation\Response    A Symfony Response object
    */
    public function view_link($link_id)
    {
        return $this->link->view($link_id);
    }

    /**
    * Vote for a link
    *
    * @param    int $cat_id        The category ID
    * @param    int $link_id    The link ID
    * @return    \Symfony\Component\HttpFoundation\Response    A Symfony Response object
    */
    public function vote_link($cat_id, $link_id)
    {
        $this->categorie->get($cat_id);

        if (!$this->auth->acl_get('u_vote_dir') || !$this->categorie->data['cat_allow_votes'])
        {
            throw new \phpbb\exception\http_exception(403, 'DIR_ERROR_NOT_AUTH');
        }

        $data = array(
            'vote_link_id'         => (int) $link_id,
            'vote_user_id'         => (int) $this->user->data['user_id'],
        );

        // We check if user had already vot for this website.
        $sql = 'SELECT vote_link_id
            FROM ' . $this->votes_table . '
            WHERE ' . $this->db->sql_build_array('SELECT', $data);
        $result = $this->db->sql_query($sql);
        $data = $this->db->sql_fetchrow($result);

        if (!empty($data['vote_link_id']))
        {
            throw new \phpbb\exception\http_exception(403, 'DIR_ERROR_VOTE');
        }

        $this->link->add_vote($link_id, $this->request->variable('vote', 0));

        $meta_info = $this->helper->route('nextgen_phpbbdirectory_dynamic_route_' . $cat_id);
        meta_refresh(3, $meta_info);
        $message = $this->language->lang('DIR_VOTE_OK') . '<br /><br />' . $this->language->lang('DIR_CLICK_RETURN_CAT', '<a href="' . $meta_info . '">', '</a>');
        return $this->helper->message($message);
    }

	/**
    * Routine
    *
    * @param    int        $cat_id        The category ID
    * @param    int        $link_id    The link ID
    * @param    string    $mode        add|edit
    * @return    null|\Symfony\Component\HttpFoundation\Response    A Symfony Response object
    */
    private function _data_processing($cat_id, $link_id = 0, $mode = 'new')
    {
        if (($mode == 'edit' && !$this->auth->acl_get('m_edit_dir') && !$this->auth->acl_get('u_edit_dir')) || ($mode == 'new' && !$this->auth->acl_get('u_submit_dir')))
        {
            throw new \phpbb\exception\http_exception(403, 'DIR_ERROR_NOT_AUTH');
        }

        if (!check_form_key('dir_form'))
        {
            return $this->helper->message('FORM_INVALID');
        }

        // 1. Captura de variables básicas
        $this->url          = $this->request->variable('url', '');
        $this->site_name    = $this->request->variable('site_name', '', true);
        $this->description  = $this->request->variable('description', '', true);
        $this->guest_email  = $this->request->variable('guest_email', '');
        $this->rss          = $this->request->variable('rss', '');
        $this->back         = $this->request->variable('back', '');
        $this->flag         = $this->request->variable('flag', '');
        
        // Variables para el manejo de archivos
        $delete_banner   = $this->request->variable('delete_banner', false);
        $old_banner_name = $this->request->variable('old_banner', '');
        $destination     = $this->root_path . 'images/directory/banners/';
        $upload_file     = $this->request->file('banner_upload');
        
        // Inicializamos banner con el valor antiguo por defecto
        $this->banner    = $old_banner_name;

        // 2. Lógica de Borrado Físico y de Base de Datos
        if ($delete_banner && !empty($old_banner_name))
        {
            $old_file = $destination . $old_banner_name;
            if (file_exists($old_file))
            {
                @unlink($old_file);
            }
            $this->banner = ''; // Limpiamos para la Base de Datos
            $old_banner_name = ''; // Evitamos que la subida intente procesarlo
        }

        // 3. Lógica de Subida de Archivo (Tiene prioridad sobre el campo de texto)
        if (!empty($upload_file['name']))
        {
            $upload_helper = $this->files_factory->get('upload');
            $upload_helper->set_allowed_extensions(['jpg', 'jpeg', 'gif', 'png']);
            
            $file = $upload_helper->handle_upload('files.types.form', 'banner_upload');

            if ($file->is_uploaded())
            {
                if (!file_exists($destination))
                {
                    mkdir($destination, 0755, true);
                    file_put_contents($destination . 'index.htm', '');
                }

                if (is_writable($destination))
                {
                    $real_name = $file->get('realname');
                    $file->move_file($destination, false, $real_name);

                    // Borrar el anterior si existe
                    if (!empty($old_banner_name) && $old_banner_name !== $real_name)
                    {
                        $old_file = $destination . $old_banner_name;
                        if (file_exists($old_file))
                        {
                            @unlink($old_file);
                        }
                    }
                    $this->banner = $real_name;
                }
                else
                {
                    $this->error[] = 'ERROR_WRITABLE_DIR'; // Asegúrate de tener esta clave en tu lang
                }
            }
            else
            {
                $error_msg = $file->error;
                $this->error[] = $this->language->lang(array_shift($error_msg), $error_msg);
            }
        }
        else if (!$delete_banner)
        {
            // 4. Si NO se marcó borrar y NO se subió archivo, miramos el campo de texto (URL manual)
            $banner_url = $this->request->variable('banner', '');
            
            // Si el campo de texto está vacío pero teníamos un banner viejo, se mantiene $this->banner = $old_banner_name
            // Si el campo de texto tiene algo nuevo (como una URL), lo usamos
            if (!empty($banner_url) && $banner_url !== $old_banner_name)
            {
                $this->banner = $banner_url;
            }
        }

        // 5. Validación de datos (phpBB estándar)
        if (!function_exists('validate_data'))
        {
            include($this->root_path . 'includes/functions_user.' . $this->php_ext);
        }

        $data = array(
            'email'       => $this->guest_email,
            'site_name'   => $this->site_name,
            'website'     => $this->url,
            'description' => $this->description,
            'rss'         => $this->rss,
            'banner'      => $this->banner,
            'back'        => $this->back,
            'cat'         => (int) $cat_id,
        );

        $data2 = array(
            'email'     => array(array('string', $this->user->data['is_registered'], 6, 60), array('user_email', '')),
            'site_name' => array(array('string', false, 1, 100)),
            'website'   => array(array('string', false, 12, 255), array('match', true, '#^http[s]?://(.*?\.)*?[a-z0-9\-]+\.[a-z]{2,4}#i')),
            'description' => array(array('string', !$this->categorie->data['cat_must_describe'], 1, $this->config['dir_length_describe'])),
            'rss'       => array(array('string', true, 12, 255), array('match', empty($this->rss), '#^http[s]?://(.*?\.)*?[a-z0-9\-]+\.[a-z]{2,4}#i')),
            'banner'    => array(array('string', true, 0, 255)), // Bajamos a 0 para permitir vacíos al borrar
            'back'      => array(array('string', !$this->categorie->data['cat_link_back'], 12, 255), array(array($this->link, 'link_back'), true)),
            'cat'       => array(array('num', '', 1))
        );

        $this->language->add_lang('ucp');
        $error = validate_data($data, $data2);
        $error = array_map(array($this->language, 'lang'), $error);

        if (preg_match('/^(http|https):\/\//si', $this->url) && $this->config['dir_activ_checkurl'] && !$this->link->checkurl($this->url))
        {
            $error[] = $this->language->lang('DIR_ERROR_CHECK_URL');
        }

        // 6. Procesamiento final
        if (!$error)
        {
            if (preg_match('/^(http|https):\/\//si', $this->banner)) 
            {
                // Pasamos $this->banner por referencia para que banner_process lo actualice con el nombre del archivo local
                $this->link->banner_process($this->banner, $error);
            }
            $thumb = $this->link->thumb_process($this->url);
        }

        if (!$error)
        {
            $this->url = $this->link->clean_url($this->url);

            $data_edit = array(
                'link_user_id'     => ($mode == 'edit') ? $this->link_user_id : $this->user->data['user_id'],
                'link_guest_email' => $this->guest_email,
                'link_name'        => $this->site_name,
                'link_url'         => $this->url,
                'link_description' => $this->description,
                'link_cat'         => (int) $cat_id,
                'link_rss'         => $this->rss,
                'link_banner'      => $this->banner, // Aquí irá el nombre local o la URL final
                'link_back'        => $this->back,
                'link_uid'         => '',
                'link_flags'       => 7,
                'link_flag'        => $this->flag,
                'link_bitfield'    => '',
                'link_thumb'       => $thumb,
            );

            if ($this->description)
            {
                generate_text_for_storage($data_edit['link_description'], $data_edit['link_uid'], $data_edit['link_bitfield'], $data_edit['link_flags'], (bool) $this->config['allow_bbcode'], (bool) $this->config['allow_post_links'], (bool) $this->config['allow_smilies'], (bool) $this->config['allow_bbcode'], ($this->config['allow_bbcode'] && $this->config['allow_post_flash']), true, (bool) $this->config['allow_post_links']);
            }

            $need_approval = ($this->categorie->need_approval() && !$this->auth->acl_get('a_') && !$this->auth->acl_get('m_')) ? true : false;

            if ($mode == 'edit')
            {
                $data_edit['link_cat_old'] = $this->request->variable('old_cat_id', 0);
                $this->link->edit($data_edit, $link_id, $need_approval);
            }
            else
            {
                $data_add = array(
                    'link_time'   => time(),
                    'link_view'   => 0,
                    'link_active' => $need_approval ? false : true,
                );
                $this->link->add(array_merge($data_edit, $data_add), $need_approval);
            }

            $meta_info = $this->helper->route('nextgen_phpbbdirectory_dynamic_route_' . $cat_id);
            meta_refresh(3, $meta_info);
            $message = ($need_approval) ? $this->language->lang('DIR_'.strtoupper($mode).'_SITE_ACTIVE') : $this->language->lang('DIR_'.strtoupper($mode).'_SITE_OK');
            return $this->helper->message($message . '<br /><br />' . $this->language->lang('DIR_CLICK_RETURN_DIR', '<a href="' . $this->helper->route('nextgen_phpbbdirectory_base_controller') . '">', '</a>'));
        }

        // --- SOLUCIÓN PARA EVITAR EL ERROR 404 / NO ROUTE FOUND ---
        $banner_view = $this->banner;
        if ($this->banner && !preg_match('/^(http|https):\/\//si', $this->banner))
        {
            // Generamos la URL a través de la ruta definida en el routing.yml
            $banner_view = $this->helper->route('nextgen_phpbbdirectory_banner_route', array(
                'banner_img' => $this->banner
            ));
        }

        // Re-asignar si hay errores
        $this->s_hidden_fields = array(
            'old_cat_id' => $this->request->variable('old_cat_id', $cat_id),
            'old_banner' => $old_banner_name,
        );

        $this->template->assign_vars(array(
            'ERROR'           => implode('<br />', $error),
            'BANNER'          => $banner_view, // Enviamos la ruta de Symfony a la plantilla
            'S_HIDDEN_FIELDS' => build_hidden_fields($this->s_hidden_fields),
        ));
    }
	
/**
* Limpia banners huérfanos que no están en la base de datos
*/
public function clean_orphan_banners()
{
    // Solo permitimos que administradores ejecuten esto por seguridad
    if (!$this->auth->acl_get('a_'))
    {
        throw new \phpbb\exception\http_exception(403, 'NOT_AUTHORISED');
    }

    $destination = $this->root_path . 'images/directory/banners/';
    
    // 1. Obtener todos los banners registrados en la DB
    $sql = 'SELECT link_banner FROM ' . $this->table_prefix . 'dir_links 
            WHERE link_banner <> ""';
    $result = $this->db->sql_query($sql);
    
    $db_banners = [];
    while ($row = $this->db->sql_fetchrow($result))
    {
        // Solo nos interesan nombres de archivos locales, no URLs http
        if (!preg_match('/^(http|https):\/\//si', $row['link_banner']))
        {
            $db_banners[] = $row['link_banner'];
        }
    }
    $this->db->sql_freeresult($result);

    // 2. Escanear la carpeta física
    $files_in_folder = array_diff(scandir($destination), array('.', '..', 'index.htm'));
    
    $deleted_count = 0;
    foreach ($files_in_folder as $file)
    {
        // Si el archivo de la carpeta NO está en el array de la DB, es un huérfano
        if (!in_array($file, $db_banners))
        {
            if (@unlink($destination . $file))
            {
                $deleted_count++;
            }
        }
    }

    return $this->helper->message("Limpieza completada. Se eliminaron $deleted_count archivos huérfanos.");
}

/**
    * Display a banner
    *
    * @param    string $banner_img        Path to banner file
    * @return   Response object
    */
    public function return_banner($banner_img)
    {
        // 1. Limpieza de seguridad (evita hackeos de ruta)
        $banner_img = basename($banner_img);
        
        // Obtenemos la ruta física
        $file_path = $this->root_path . $this->get_banner_path($banner_img);

        if (file_exists($file_path) && is_readable($file_path))
        {
            // Usamos BinaryFileResponse de Symfony
            $response = new \Symfony\Component\HttpFoundation\BinaryFileResponse($file_path);
            
            // 2. Configuración de Caché (Vital para rendimiento)
            $response->setPublic();
            $response->setMaxAge(3600); // Guardar en caché 1 hora
            $response->headers->addCacheControlDirective('must-revalidate', true);
            
            // 3. Detección ROBUSTA del tipo de imagen
            // No dependemos de si tienes fileinfo o no, lo verificamos siempre.
            $mime_type = 'application/octet-stream'; // Fallback
            
            // Intentamos con getimagesize (Nativo y fiable para imágenes)
            $img_info = @getimagesize($file_path);
            if ($img_info && isset($img_info['mime']))
            {
                $mime_type = $img_info['mime'];
            }
            // Si falla, intentamos con mime_content_type
            else if (function_exists('mime_content_type')) 
            {
                $mime_type = mime_content_type($file_path);
            }

            // Forzamos la cabecera correcta
            $response->headers->set('Content-Type', $mime_type);
            $response->setContentDisposition('inline', $banner_img);

            return $response;
        }

        // Si no existe la imagen
        return new \Symfony\Component\HttpFoundation\Response('Banner not found', 404);
    }

    /**
    * Populate form when an error occurred
    *
    * @param    int        $cat_id        The category ID
    * @param    string    $mode        add|edit
    * @param    string    $title        Page title (depends of $mode)
    * @return    null
    */
    private function _populate_form($cat_id, $mode, $title)
    {
        global $phpbb_extension_manager;

        if (!$this->user->data['is_registered'] && $this->config['dir_visual_confirm'] && $mode == 'new')
        {
            $this->s_hidden_fields = array_merge($this->s_hidden_fields, $this->captcha->get_hidden_fields());

            $this->language->add_lang('ucp');

            $this->template->assign_vars(array(
                'CAPTCHA_TEMPLATE'        => $this->captcha->get_template(),
            ));
        }

        $this->language->add_lang('posting');

        if (!function_exists('display_custom_bbcodes'))
        {
            include($this->root_path . 'includes/functions_display.' . $this->php_ext);
        }
        display_custom_bbcodes();
        add_form_key('dir_form');

        $ext_path = $phpbb_extension_manager->get_extension_path('nextgen/phpbbdirectory', false);
        $flag_path = $ext_path.'images/flags/';

        $s_guest    = (!$this->user->data['is_registered'] || !empty($this->guest_email));
        $s_rss        = $this->config['dir_activ_rss'];
        $s_banner    = $this->config['dir_activ_banner'];
        $s_back        = $this->categorie->data['cat_link_back'];
        $s_flag        = $this->config['dir_activ_flag'];

        $this->template->assign_vars(array(
            'BBCODE_STATUS'            => ($this->config['allow_bbcode'])     ? $this->language->lang('BBCODE_IS_ON', '<a href="' . append_sid($this->root_path."faq.$this->php_ext", 'mode=bbcode') . '">', '</a>') : $this->language->lang('BBCODE_IS_OFF', '<a href="' . append_sid($this->root_path."faq.$this->php_ext", 'mode=bbcode') . '">', '</a>'),
            'IMG_STATUS'            => ($this->config['allow_bbcode'])    ? $this->language->lang('IMAGES_ARE_ON') : $this->language->lang('IMAGES_ARE_OFF'),
            'SMILIES_STATUS'        => ($this->config['allow_smilies']) ? $this->language->lang('SMILIES_ARE_ON') : $this->language->lang('SMILIES_ARE_OFF'),
            'URL_STATUS'            => ($this->config['allow_post_links']) ? $this->language->lang('URL_IS_ON') : $this->language->lang('URL_IS_OFF'),
            'FLASH_STATUS'            => ($this->config['allow_bbcode'] && $this->config['allow_post_flash'])    ? $this->language->lang('FLASH_IS_ON') : $this->language->lang('FLASH_IS_OFF'),

            'L_TITLE'                => $title,
            'L_DIR_DESCRIPTION_EXP'    => $this->language->lang('DIR_DESCRIPTION_EXP', $this->config['dir_length_describe']),
            'L_DIR_SUBMIT_TYPE'        => $this->categorie->dir_submit_type($this->categorie->need_approval()),
            'L_DIR_SITE_BANN_EXP'    => $this->language->lang('DIR_SITE_BANN_EXP', $this->config['dir_banner_width'], $this->config['dir_banner_height']),

            'S_GUEST'                => $s_guest ? true : false,
            'S_RSS'                    => $s_rss ? true : false,
            'S_BANNER'                => $s_banner ? true : false,
            'S_BACK'                => $s_back ? true : false,
            'S_FLAG'                => $s_flag ? true : false,
            'S_BBCODE_ALLOWED'         => (bool) $this->config['allow_bbcode'],
            'S_BBCODE_IMG'            => (bool) $this->config['allow_bbcode'],
            'S_BBCODE_FLASH'        => ($this->config['allow_bbcode'] && $this->config['allow_post_flash']) ? true : false,
            'S_BBCODE_QUOTE'        => true,
            'S_LINKS_ALLOWED'        => (bool) $this->config['allow_post_links'],

            'DIR_FLAG_PATH'            => $flag_path,
            'DIR_FLAG_IMAGE'        => $this->flag ? $this->get_img_path('flags', $this->flag) : '',

            'EDIT_MODE'                => ($mode == 'edit') ? true : false,

            'SITE_NAME'                => isset($this->site_name) ? $this->site_name : '',
            'SITE_URL'                => isset($this->url) ? $this->url : '',
            'DESCRIPTION'            => isset($this->description) ? $this->description : '',
            'GUEST_EMAIL'            => isset($this->guest_email) ? $this->guest_email : '',
            'RSS'                    => isset($this->rss) ? $this->rss : '',
            'BANNER'                => isset($this->banner) ? $this->banner : '',
            'BACK'                    => isset($this->back) ? $this->back : '',
            'S_POST_ACTION'            => '',
            'S_CATLIST'                => $this->categorie->make_cat_select($cat_id),
            'S_LIST_FLAG'            => $this->link->get_dir_flag_list($flag_path, $this->flag),
            'S_DESC_STAR'            => (@$this->categorie->data['cat_must_describe']) ? '*' : '',
            'S_ROOT'                => $cat_id,
            'S_HIDDEN_FIELDS'        => build_hidden_fields($this->s_hidden_fields),
        ));
    }
}