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

use \nextgen\phpbbdirectory\core\helper;

class comments extends helper
{
	private $captcha;
	private $s_comment;
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

	/** @var \phpbb\pagination */
	protected $pagination;

	/** @var \phpbb\captcha\factory */
	protected $captcha_factory;

	/** @var \nextgen\phpbbdirectory\core\categorie */
	protected $categorie;

	/** @var \nextgen\phpbbdirectory\core\comment */
	protected $comment;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string phpEx */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface					$db					Database object
	* @param \phpbb\config\config								$config				Config object
	* @param \phpbb\language\language							$language			Language object
	* @param \phpbb\template\template							$template			Template object
	* @param \phpbb\user										$user				User object
	* @param \phpbb\controller\helper							$helper				Controller helper object
	* @param \phpbb\request\request								$request			Request object
	* @param \phpbb\auth\auth									$auth				Auth object
	* @param \phpbb\pagination									$pagination			Pagination object
	* @param \phpbb\captcha\factory								$captcha_factory	Captcha object
	* @param \nextgen\phpbbdirectory\core\categorie				$categorie			PhpBB Directory extension categorie object
	* @param \nextgen\phpbbdirectory\core\comment				$comment			PhpBB Directory extension comment object
	* @param string												$root_path			phpBB root path
	* @param string												$php_ext			phpEx
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\config\config $config, \phpbb\language\language $language, \phpbb\template\template $template, \phpbb\user $user, \phpbb\controller\helper $helper, \phpbb\request\request $request, \phpbb\auth\auth $auth, \phpbb\pagination $pagination, \phpbb\captcha\factory $captcha_factory, \nextgen\phpbbdirectory\core\categorie $categorie, \nextgen\phpbbdirectory\core\comment $comment, $root_path, $php_ext)
	{
		$this->db				= $db;
		$this->config			= $config;
		$this->language			= $language;
		$this->template			= $template;
		$this->user				= $user;
		$this->helper			= $helper;
		$this->request			= $request;
		$this->auth				= $auth;
		$this->pagination		= $pagination;
		$this->captcha_factory 	= $captcha_factory;
		$this->categorie		= $categorie;
		$this->comment			= $comment;
		$this->root_path		= $root_path;
		$this->php_ext			= $php_ext;

		$template->assign_vars(array(
			'S_PHPBB_DIRECTORY'	=> true,
			'S_SIMPLE_MESSAGE' 	=> true,
		));

		// The CAPTCHA kicks in here. We can't help that the information gets lost on language change.
		if (!$this->user->data['is_registered'] && $this->config['dir_visual_confirm'])
		{
			$this->captcha = $this->captcha_factory->get_instance($this->config['captcha_plugin']);
			$this->captcha->init(CONFIRM_POST);
		}
	}

	/**
	* Delete an existing comment.
	*
	* Handles permission checks, confirmation dialog and final deletion
	* of a comment associated with a directory link.
	*
	* @param int $link_id    The link ID the comment belongs to
	* @param int $comment_id The comment ID to be deleted
	*
	* @return \Symfony\Component\HttpFoundation\Response|null
	*
	* @throws \phpbb\exception\http_exception
	*/
	public function delete_comment($link_id, $comment_id)
	{
		$this->_check_comments_enable($link_id);

		if ($this->request->is_set_post('cancel'))
		{
			$redirect = $this->helper->route('nextgen_phpbbdirectory_comment_view_controller', array('link_id' => (int) $link_id));
			redirect($redirect);
		}

		$sql = 'SELECT *
			FROM ' . $this->comments_table . '
			WHERE comment_id = ' . (int) $comment_id;
		$result = $this->db->sql_query($sql);
		$value = $this->db->sql_fetchrow($result);

		if (!$this->user->data['is_registered'] || !$this->auth->acl_get('m_delete_comment_dir') && (!$this->auth->acl_get('u_delete_comment_dir') || $this->user->data['user_id'] != $value['comment_user_id']))
		{
			throw new \phpbb\exception\http_exception(403, 'DIR_ERROR_NOT_AUTH');
		}

		if (confirm_box(true))
		{
			$this->comment->del($link_id, $comment_id);

			$meta_info = $this->helper->route('nextgen_phpbbdirectory_comment_view_controller', array('link_id' => (int) $link_id));
			meta_refresh(3, $meta_info);
			$message = $this->language->lang('DIR_COMMENT_DELETE_OK');
			$message = $message . '<br /><br />' . $this->language->lang('DIR_CLICK_RETURN_COMMENT', '<a href="' . $meta_info . '">', '</a>');
			return $this->helper->message($message);
		}
		else
		{
			confirm_box(false, 'DIR_COMMENT_DELETE');
		}
	}

	/**
	* Edit an existing comment.
	*
	* Loads the comment data, checks edit permissions and either
	* displays the edit form or processes the submitted changes.
	*
	* @param int $link_id    The link ID the comment belongs to
	* @param int $comment_id The comment ID to edit
	*
	* @return \Symfony\Component\HttpFoundation\Response|null
	*
	* @throws \phpbb\exception\http_exception
	*/
	public function edit_comment($link_id, $comment_id)
	{
		$this->_check_comments_enable($link_id);

		$sql = 'SELECT *
			FROM ' . $this->comments_table . '
			WHERE comment_id = ' . (int) $comment_id;
		$result = $this->db->sql_query($sql);
		$value = $this->db->sql_fetchrow($result);

		if (!$this->user->data['is_registered'] || !$this->auth->acl_get('m_edit_comment_dir') && (!$this->auth->acl_get('u_edit_comment_dir') || $this->user->data['user_id'] != $value['comment_user_id']))
		{
			throw new \phpbb\exception\http_exception(403, 'DIR_ERROR_NOT_AUTH');
		}

		$comment_text = generate_text_for_edit($value['comment_text'], $value['comment_uid'], $value['comment_flags']);
		$this->s_comment = $comment_text['text'];

		$submit	= $this->request->is_set_post('update_comment') ? true : false;

		// If form is done
		if ($submit)
		{
			return $this->_data_processing($link_id, $comment_id, 'edit');
		}

		return $this->view($link_id, 1, 'edit');
	}

	/**
	* Create a new comment for a directory link.
	*
	* Handles both normal form submission and AJAX-based submission.
	* When called via AJAX, the comment is processed and returned
	* without performing a redirect.
	*
	* @param int $link_id The link ID to attach the new comment to
	*
	* @return \Symfony\Component\HttpFoundation\Response|null
	*
	* @throws \phpbb\exception\http_exception
	*/
	public function new_comment($link_id)
	{
		$this->_check_comments_enable($link_id);

		if (!$this->auth->acl_get('u_comment_dir'))
		{
			throw new \phpbb\exception\http_exception(403, 'DIR_ERROR_NOT_AUTH');
		}

		$submit		= $this->request->is_set_post('submit_comment') ? true : false;
		$refresh	= $this->request->is_set_post('refresh_vc') ? true : false;

		// If form is done
		if ($submit || $refresh)
		{
		// 👉 Si es AJAX, NO redireccionar
		if ($this->request->is_ajax())
		{
			return $this->ajax_post($link_id);
		}

		return $this->_data_processing($link_id);
		}

		else
		{
			$redirect = $this->helper->route('nextgen_phpbbdirectory_comment_view_controller', array('link_id' => (int) $link_id));
			redirect($redirect);
		}
	}

	/**
	* Load and display comments for a directory link.
	*
	* This method is used for both standard page rendering and
	* AJAX-based comment loading. It handles pagination, permissions,
	* template assignment and comment formatting.
	*
	* @param int    $link_id The link ID whose comments are displayed
	* @param int    $page    Page number for pagination
	* @param string $mode    Current mode (new|edit)
	*
	* @return \Symfony\Component\HttpFoundation\Response
	*
	* @throws \phpbb\exception\http_exception
	*/
	public function view($link_id, $page = 1, $mode = 'new')
	{

		$this->_check_comments_enable($link_id);

		$comment_id = $this->request->variable('c', 0);
		$view       = $this->request->variable('view', '');


		$per_page = (int) $this->config['dir_comments_per_page'];
		$start    = ($page - 1) * $per_page;


		$this->s_hidden_fields['page'] = $page;

		$this->_populate_form($link_id, $mode);


		$sql = 'SELECT COUNT(comment_id) AS total
			FROM ' . $this->comments_table . '
			WHERE comment_link_id = ' . (int) $link_id;

		$result = $this->db->sql_query($sql);
		$total_comments = (int) $this->db->sql_fetchfield('total');
		$this->db->sql_freeresult($result);


		$start = $this->pagination->validate_start($start, $per_page, $total_comments);

		$sql_array = [
			'SELECT' => '
				a.comment_id,
				a.comment_user_id,
				a.comment_user_ip,
				a.comment_date,
				a.comment_text,
				a.comment_uid,
				a.comment_bitfield,
				a.comment_flags,
				u.username,
				u.user_colour,
				z.foe
			',
			'FROM' => [
				$this->comments_table => 'a'
			],
			'LEFT_JOIN' => [
				[
					'FROM' => [USERS_TABLE => 'u'],
					'ON'   => 'a.comment_user_id = u.user_id'
				],
				[
					'FROM' => [ZEBRA_TABLE => 'z'],
					'ON'   => 'z.user_id = ' . (int) $this->user->data['user_id'] . '
							AND z.zebra_id = a.comment_user_id'
				]
			],
			'WHERE'    => 'a.comment_link_id = ' . (int) $link_id,
			'ORDER_BY' => 'a.comment_date DESC'
		];

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, $per_page, $start);

		$have_result = false;

		while ($row = $this->db->sql_fetchrow($result))
		{
			$have_result = true;


			$can_edit = (
				$this->user->data['is_registered'] &&
				(
					$this->auth->acl_get('m_edit_comment_dir') ||
					(
					$this->user->data['user_id'] == $row['comment_user_id'] &&
					$this->auth->acl_get('u_edit_comment_dir')
					)
				)
			);

			$can_delete = (
				$this->user->data['is_registered'] &&
				(
					$this->auth->acl_get('m_delete_comment_dir') ||
					(
					$this->user->data['user_id'] == $row['comment_user_id'] &&
					$this->auth->acl_get('u_delete_comment_dir')
					)
				)
			);


			$this->template->assign_block_vars('comment', [
				'S_ID'      => $row['comment_id'],
				'S_USER'    => get_username_string(
					'full',
					$row['comment_user_id'],
					$row['username'],
					$row['user_colour']
				),
				'S_DATE'    => $this->user->format_date($row['comment_date']),
				'S_COMMENT' => generate_text_for_display(
					$row['comment_text'],
					$row['comment_uid'],
					$row['comment_bitfield'],
					$row['comment_flags']
				),

				'U_EDIT'   => $can_edit
					? $this->helper->route(
						'nextgen_phpbbdirectory_comment_edit_controller',
						['link_id' => $link_id, 'comment_id' => $row['comment_id']]
					)
					: '',

				'U_DELETE' => $can_delete
					? $this->helper->route(
						'nextgen_phpbbdirectory_comment_delete_controller',
						[
							'link_id'    => $link_id,
							'comment_id' => $row['comment_id'],
							'_referer'   => $this->helper->get_current_url()
						]
					)
				: '',
			]);
		}

		$this->db->sql_freeresult($result);


		$this->pagination->generate_template_pagination(
			[
				'routes' => 'nextgen_phpbbdirectory_comment_view_controller',
				'params' => ['link_id' => $link_id],
			],
			'pagination',
			'page',
			$total_comments,
			$per_page,
			$start
		);


		$this->template->assign_vars([
			'LINK_ID'         => (int) $link_id,
			'TOTAL_COMMENTS'  => $this->language->lang('DIR_NB_COMMS', $total_comments),
			'S_HAVE_RESULT'   => $have_result,
		]);

		return $this->helper->render(
			'comments.html',
			$this->language->lang('DIR_COMMENT_TITLE')
		);
	}

/**
    * Handle AJAX comment submission.
    *
    * Processes the comment, saves it to the database, and returns the updated
    * comment list and a fresh form token via JSON.
    *
    * @param int $link_id The link ID to attach the comment to
    * @return \Symfony\Component\HttpFoundation\JsonResponse
    */
    public function ajax_post($link_id)
    {
        // 1. Pre-checks: Enable status, Permissions and AJAX request
        $this->_check_comments_enable($link_id);

        if (!$this->auth->acl_get('u_comment_dir'))
        {
            return new \Symfony\Component\HttpFoundation\JsonResponse([
                'error' => $this->language->lang('DIR_ERROR_NOT_AUTH')
            ], 403);
        }

        if (!$this->request->is_ajax())
        {
            return new \Symfony\Component\HttpFoundation\JsonResponse([
                'error' => 'Bad Request'
            ], 400);
        }

        // 2. Validate Form Key (Security Token)
        if (!check_form_key('dir_form_comment'))
        {
            return new \Symfony\Component\HttpFoundation\JsonResponse([
                'error'     => $this->language->lang('FORM_INVALID'),
                'new_token' => add_form_key('dir_form_comment'), // Refresh token on failure
            ]);
        }

        // 3. Data Validation
        $this->s_comment = $this->request->variable('message', '', true);

        if (!function_exists('validate_data'))
        {
            include($this->root_path . 'includes/functions_user.' . $this->php_ext);
        }

        $error = validate_data(
            ['reply' => $this->s_comment],
            ['reply' => [['string', false, 1, $this->config['dir_length_comments']]]]
        );

        if ($error)
        {
            $error_msg = array_map([$this->language, 'lang'], $error);
            return new \Symfony\Component\HttpFoundation\JsonResponse([
                'error' => implode('<br>', $error_msg)
            ]);
        }

        // 4. Prepare text for storage (BBCode/Smilies parsing)
        $uid = $bitfield = $flags = '';
        generate_text_for_storage(
            $this->s_comment,
            $uid,
            $bitfield,
            $flags,
            (bool) $this->config['dir_allow_bbcode'],
            (bool) $this->config['dir_allow_links'],
            (bool) $this->config['dir_allow_smilies'],
            (bool) $this->config['dir_allow_bbcode'],
            ($this->config['dir_allow_bbcode'] && $this->config['dir_allow_flash']),
            true,
            (bool) $this->config['dir_allow_links']
        );

        // 5. Save Comment
        $this->comment->add([
            'comment_link_id'  => (int) $link_id,
            'comment_date'     => time(),
            'comment_user_id'  => (int) $this->user->data['user_id'],
            'comment_user_ip'  => $this->user->ip,
            'comment_text'     => $this->s_comment,
            'comment_uid'      => $uid,
            'comment_flags'    => $flags,
            'comment_bitfield' => $bitfield,
        ]);

        // 6. Generate updated HTML list
        // We call view() to populate template variables for the specific link
        $this->view($link_id, 1);

        // Ensure no previous output (like PHP notices) interferes with JSON
        if (ob_get_length())
        {
            ob_clean();
        }

        ob_start();
        $this->template->display('comments_list.html');
        $html = ob_get_clean();

        // 7. Get updated total
        $sql = 'SELECT COUNT(comment_id) AS total
                FROM ' . $this->comments_table . '
                WHERE comment_link_id = ' . (int) $link_id;

        $result = $this->db->sql_query($sql);
        $total = (int) $this->db->sql_fetchfield('total');
        $this->db->sql_freeresult($result);

        // 8. Final Response
        return new \Symfony\Component\HttpFoundation\JsonResponse([
            'html'      => $html,
            'total'     => $total,
            'new_token' => add_form_key('dir_form_comment'), // Token for the next submission
        ]);
    }

	/**
	* Process comment form data.
	*
	* Validates input, handles CAPTCHA (if enabled) and either
	* inserts or updates a comment depending on the current mode.
	*
	* @param int    $link_id    The link ID
	* @param int    $comment_id The comment ID (used when editing)
	* @param string $mode       Operation mode (new|edit)
	*
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	private function _data_processing($link_id, $comment_id = 0, $mode = 'new')
	{
		if (!check_form_key('dir_form_comment'))
		{
			return $this->helper->message('FORM_INVALID');
		}

		$this->s_comment = $this->request->variable('message', '', true);

		if (!function_exists('validate_data'))
		{
			include($this->root_path . 'includes/functions_user.' . $this->php_ext);
		}

		$error = validate_data(
			array(
				'reply' => $this->s_comment),
			array(
				'reply' => array(
					array('string', false, 1, $this->config['dir_length_comments'])
				)
			)
		);

		$error = array_map(array($this->language, 'lang'), $error);

		if (!$this->user->data['is_registered'] && $this->config['dir_visual_confirm'])
		{
			$vc_response = $this->captcha->validate();
			if ($vc_response !== false)
			{
				$error[] = $vc_response;
			}

			if ($this->config['dir_visual_confirm_max_attempts'] && $this->captcha->get_attempt_count() > $this->config['dir_visual_confirm_max_attempts'])
			{
				$error[] = $this->language->lang('TOO_MANY_ADDS');
			}
		}

		if (!$error)
		{
			$uid = $bitfield = $flags = '';
			generate_text_for_storage($this->s_comment, $uid, $bitfield, $flags, (bool) $this->config['dir_allow_bbcode'], (bool) $this->config['dir_allow_links'], (bool) $this->config['dir_allow_smilies'], (bool) $this->config['dir_allow_bbcode'], ($this->config['dir_allow_bbcode'] && $this->config['dir_allow_flash']), true, (bool) $this->config['dir_allow_links']);

			$data_edit = array(
				'comment_text'		=> $this->s_comment,
				'comment_uid'		=> $uid,
				'comment_flags'		=> $flags,
				'comment_bitfield'	=> $bitfield,
			);

			if ($mode == 'edit')
			{
				$this->comment->edit($data_edit, $comment_id);
			}
			else
			{
				$data_add = array(
					'comment_link_id'	=> (int) $link_id,
					'comment_date'		=> time(),
					'comment_user_id'	=> $this->user->data['user_id'],
					'comment_user_ip'	=> $this->user->ip,
				);

				$data_add = array_merge($data_edit, $data_add);

				$this->comment->add($data_add);
			}

			$meta_info = $this->helper->route('nextgen_phpbbdirectory_comment_view_controller', array('link_id' => (int) $link_id));
			meta_refresh(3, $meta_info);
			$message = $this->language->lang('DIR_'.strtoupper($mode).'_COMMENT_OK');
			$message = $message . '<br /><br />' . $this->language->lang('DIR_CLICK_RETURN_COMMENT', '<a href="' . $meta_info . '">', '</a>');
			return $this->helper->message($message);
		}
		else
		{
			$this->template->assign_vars(array(
				'ERROR'	=> (sizeof($error)) ? implode('<br />', $error) : ''
			));

			return $this->view($link_id, $this->request->variable('page', 1), $mode);
		}
	}

	/**
	* Check if comments are enable in a category
	*
	* @param	int		$link_id		The link ID
	* @return	null					Retun null if comments are allowed, http_exception if not
	* @throws	\phpbb\exception\http_exception
	*/
	private function _check_comments_enable($link_id)
	{
		$sql = 'SELECT link_cat
			FROM ' . $this->links_table . '
			WHERE link_id = ' . (int) $link_id;
		$result = $this->db->sql_query($sql);
		$cat_id = (int) $this->db->sql_fetchfield('link_cat');
		$this->db->sql_freeresult($result);

		if ($cat_id)
		{
			$this->categorie->get($cat_id);

			if (!$this->categorie->data['cat_allow_comments'])
			{
				throw new \phpbb\exception\http_exception(403, 'DIR_ERROR_NOT_AUTH');
			}
		}
		else
		{
			throw new \phpbb\exception\http_exception(404, 'DIR_ERROR_NO_LINKS');
		}
	}

	/**
	* Populate form when an error occurred
	*
	* @param	int		$link_id		The link ID
	* @param	string	$mode			add|edit
	* @return	null
	*/
	private function _populate_form($link_id, $mode)
	{
		$this->user->add_lang(array('ucp', 'posting'));

		if (!$this->user->data['is_registered'] && $this->config['dir_visual_confirm'] && $mode != 'edit')
		{
			$this->s_hidden_fields = array_merge($this->s_hidden_fields, $this->captcha->get_hidden_fields());

			$this->template->assign_vars(array(
				'S_CONFIRM_CODE'		=> true,
				'CAPTCHA_TEMPLATE'		=> $this->captcha->get_template(),
			));
		}

		if (!function_exists('generate_smilies'))
		{
			include($this->root_path . 'includes/functions_posting.' . $this->php_ext);
		}
		if (!function_exists('display_custom_bbcodes'))
		{
			include($this->root_path . 'includes/functions_display.' . $this->php_ext);
		}

		generate_smilies('inline', 0);
		display_custom_bbcodes();
		add_form_key('dir_form_comment');

		$this->template->assign_vars(array(
			'S_AUTH_COMM' 		=> $this->auth->acl_get('u_comment_dir'),

			'BBCODE_STATUS'		=> ($this->config['dir_allow_bbcode']) 	? $this->language->lang('BBCODE_IS_ON', '<a href="' . append_sid($this->root_path."faq.$this->php_ext", 'mode=bbcode') . '">', '</a>') : $this->language->lang('BBCODE_IS_OFF', '<a href="' . append_sid($this->root_path."faq.$this->php_ext", 'mode=bbcode') . '">', '</a>'),
			'IMG_STATUS'		=> ($this->config['dir_allow_bbcode']) 	? $this->language->lang('IMAGES_ARE_ON') : $this->language->lang('IMAGES_ARE_OFF'),
			'SMILIES_STATUS'	=> ($this->config['dir_allow_smilies'])	? $this->language->lang('SMILIES_ARE_ON') : $this->language->lang('SMILIES_ARE_OFF'),
			'URL_STATUS'		=> ($this->config['dir_allow_links'])	? $this->language->lang('URL_IS_ON') : $this->language->lang('URL_IS_OFF'),
			'FLASH_STATUS'		=> ($this->config['dir_allow_bbcode'] && $this->config['dir_allow_flash'])	? $this->language->lang('FLASH_IS_ON') : $this->language->lang('FLASH_IS_OFF'),

			'L_DIR_REPLY_EXP'	=> $this->language->lang('DIR_REPLY_EXP', $this->config['dir_length_comments']),

			'S_COMMENT' 		=> isset($this->s_comment) ? $this->s_comment : '',

			'S_BBCODE_ALLOWED' 	=> (bool) $this->config['dir_allow_bbcode'],
			'S_BBCODE_IMG'		=> (bool) $this->config['dir_allow_bbcode'],
			'S_BBCODE_FLASH'	=> ($this->config['dir_allow_bbcode'] && $this->config['dir_allow_flash']) ? true : false,
			'S_BBCODE_QUOTE'	=> true,
			'S_LINKS_ALLOWED'	=> (bool) $this->config['dir_allow_links'],
			'S_SMILIES_ALLOWED' => (bool) $this->config['dir_allow_smilies'],

			'S_HIDDEN_FIELDS'	=> build_hidden_fields($this->s_hidden_fields),
			'S_BUTTON_NAME'		=> ($mode == 'edit') ? 'update_comment' : 'submit_comment',
			'S_POST_ACTION' 	=> ($mode == 'edit') ? '' : $this->helper->route('nextgen_phpbbdirectory_comment_new_controller', array('link_id' => (int) $link_id)),
			'U_ADD_COMMENT_RELATIVE' => $this->helper->route('nextgen_phpbbdirectory_comment_ajax_post', array('link_id' => (int) $link_id), false),
		));
	}
}
