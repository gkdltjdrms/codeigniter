<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Posthistory class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 관리자>게시판설정>게시물변경로그 controller 입니다.
 */
class Posthistory extends CB_Controller
{

	/**
	 * 관리자 페이지 상의 현재 디렉토리입니다
	 * 페이지 이동시 필요한 정보입니다
	 */
	public $pagedir = 'board/posthistory';

	/**
	 * 모델을 로딩합니다
	 */
	protected $models = array('Post_history', 'Post', 'Board');

	/**
	 * 이 컨트롤러의 메인 모델 이름입니다
	 */
	protected $modelname = 'Post_history_model';

	/**
	 * 헬퍼를 로딩합니다
	 */
	protected $helpers = array('form', 'array');

	function __construct()
	{
		parent::__construct();

		/**
		 * 라이브러리를 로딩합니다
		 */
		$this->load->library(array('pagination', 'querystring'));
	}

	/**
	 * 목록을 가져오는 함수입니다
	 */
	public function index()
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_board_posthistory_index';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		/**
		 * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
		 */
		$param =& $this->querystring;
		$page = (((int) $this->input->get('page')) > 0) ? ((int) $this->input->get('page')) : 1;
		$findex = $this->input->get('findex') ? $this->input->get('findex') : $this->{$this->modelname}->primary_key;
		$forder = $this->input->get('forder', null, 'desc');
		$sfield = $this->input->get('sfield', null, '');
		$skeyword = $this->input->get('skeyword', null, '');

		$per_page = admin_listnum();
		$offset = ($page - 1) * $per_page;

		/**
		 * 게시판 목록에 필요한 정보를 가져옵니다.
		 */
		$this->{$this->modelname}->allow_search_field = array('phi_id', 'post_history.post_id', 'post_history.mem_id', 'phi_title', 'phi_content', 'phi_ip', 'phi_datetime'); // 검색이 가능한 필드
		$this->{$this->modelname}->search_field_equal = array('phi_id', 'post_history.post_id', 'post_history.mem_id'); // 검색중 like 가 아닌 = 검색을 하는 필드
		$this->{$this->modelname}->allow_order_field = array('phi_id'); // 정렬이 가능한 필드
		$where = array(
			'post.post_del' => 0,
		);
		if ($brdid = (int) $this->input->get('brd_id')) {
			$where['post.brd_id'] = $brdid;
		}
		$result = $this->{$this->modelname}
			->get_admin_list($per_page, $offset, $where, '', $findex, $forder, $sfield, $skeyword);
		$list_num = $result['total_rows'] - ($page - 1) * $per_page;
		if (element('list', $result)) {
			foreach (element('list', $result) as $key => $val) {
				$result['list'][$key]['display_name'] = display_username(
					element('mem_userid', $val),
					element('mem_nickname', $val),
					element('mem_icon', $val)
				);
				$result['list'][$key]['board'] = $board = $this->board->item_all(element('brd_id', $val));
				$result['list'][$key]['num'] = $list_num--;
				if ($board) {
					$result['list'][$key]['boardurl'] = board_url(element('brd_key', $board));
					$result['list'][$key]['posturl'] = post_url(element('brd_key', $board), element('post_id', $val));
				}
				$result['list'][$key]['post_display_name'] = display_username(
					element('post_userid', $val),
					element('post_nickname', $val)
				);
			}
		}
		$view['view']['data'] = $result;

		$view['view']['boardlist'] = $this->Board_model->get_board_list();

		/**
		 * primary key 정보를 저장합니다
		 */
		$view['view']['primary_key'] = $this->{$this->modelname}->primary_key;

		/**
		 * 페이지네이션을 생성합니다
		 */
		$config['base_url'] = admin_url($this->pagedir) . '?' . $param->replace('page');
		$config['total_rows'] = $result['total_rows'];
		$config['per_page'] = $per_page;
		$this->pagination->initialize($config);
		$view['view']['paging'] = $this->pagination->create_links();
		$view['view']['page'] = $page;

		/**
		 * 쓰기 주소, 삭제 주소등 필요한 주소를 구합니다
		 */
		$search_option = array('phi_title' => '제목', 'phi_content' => '내용', 'phi_ip' => 'IP', 'phi_datetime' => '변경일');
		$view['view']['skeyword'] = ($sfield && array_key_exists($sfield, $search_option)) ? $skeyword : '';
		$view['view']['search_option'] = search_option($search_option, $sfield);
		$view['view']['listall_url'] = admin_url($this->pagedir);
		$view['view']['list_delete_url'] = admin_url($this->pagedir . '/listdelete/?' . $param->output());

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

		/**
		 * 어드민 레이아웃을 정의합니다
		 */
		$layoutconfig = array('layout' => 'layout', 'skin' => 'index');
		$view['layout'] = $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data = $view;
		$this->layout = element('layout_skin_file', element('layout', $view));
		$this->view = element('view_skin_file', element('layout', $view));
	}

	/**
	 * 상세보기 페이지를 가져오는 함수입니다
	 */
	public function view($phi_id = 0)
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_board_posthistory_view';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		$phi_id = (int) $phi_id;
		if (empty($phi_id) OR $phi_id < 1) {
			show_404();
		}

		$view = array();
		$view['view'] = array();

		$param =& $this->querystring;

		$result = $this->{$this->modelname}->get_one($phi_id);

		if ( ! element('phi_id', $result)) {
			show_404();
		}

		$select = 'mem_id, mem_userid, mem_nickname, mem_icon';
		$result['member'] = $dbmember = $this->Member_model->get_by_memid(element('mem_id', $result), $select);
		$result['display_name'] = display_username(
			element('mem_userid', $dbmember),
			element('mem_nickname', $dbmember),
			element('mem_icon', $dbmember)
		);
		$select = 'post_id, brd_id, post_title, post_userid, post_nickname, post_datetime, post_ip, post_hit, post_secret';
		$result['post'] = $post = $this->Post_model->get_one(element('post_id', $result), $select);
		if ($post) {
			$result['board'] = $board = $this->board->item_all(element('brd_id', $post));
		}
		$result['post_display_name'] = display_username(
			element('post_userid', $post),
			element('post_nickname', $post)
		);
		$image_width = ($this->cbconfig->get_device_view_type() === 'mobile') ? element('post_mobile_image_width', $board) : element('post_image_width', $board);
		$result['content'] = display_html_content(
			element('phi_content', $result),
			element('phi_content_html_type', $result),
			$image_width
		);

		$where = array(
			'post_id' => element('post_id', $result),
			'phi_id <' => element('phi_id', $result),
		);
		$prev = $this->{$this->modelname}->get('', '', $where, 1, 0, 'phi_id', 'DESC');
		if ($prev && element(0, $prev)) {
			$prev[0]['content'] = display_html_content(
				$prev[0]['phi_content'],
				$prev[0]['phi_content_html_type'],
				$image_width
			);
			$result['prev'] = $prev[0];
		}

		$view['view']['data'] = $result;

		$view['view']['delete_url'] = admin_url($this->pagedir . '/delete/' . $phi_id . '?' . $param->output());

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

		/**
		 * 어드민 레이아웃을 정의합니다
		 */
		$layoutconfig = array('layout' => 'layout', 'skin' => 'view');
		$view['layout'] = $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data = $view;
		$this->layout = element('layout_skin_file', element('layout', $view));
		$this->view = element('view_skin_file', element('layout', $view));
	}

	/**
	 * 목록 페이지에서 선택삭제를 하는 경우 실행되는 메소드입니다
	 */
	public function listdelete()
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_board_posthistory_listdelete';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		/**
		 * 체크한 게시물의 삭제를 실행합니다
		 */
		if ($this->input->post('chk') && is_array($this->input->post('chk'))) {
			foreach ($this->input->post('chk') as $val) {
				if ($val) {
					$this->{$this->modelname}->delete($val);
				}
			}
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		/**
		 * 삭제가 끝난 후 목록페이지로 이동합니다
		 */
		$this->session->set_flashdata(
			'message',
			'정상적으로 삭제되었습니다'
		);
		$param =& $this->querystring;
		$redirecturl = admin_url($this->pagedir . '?' . $param->output());

		redirect($redirecturl);
	}

	/**
	 * 상세페이지에서 선택삭제를 하는 경우 실행되는 메소드입니다
	 */
	public function delete($phi_id = 0)
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_board_posthistory_delete';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$phi_id = (int) $phi_id;
		if (empty($phi_id) OR $phi_id < 1) {
			show_404();
		}

		$this->{$this->modelname}->delete($phi_id);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		/**
		 * 삭제가 끝난 후 목록페이지로 이동합니다
		 */
		$this->session->set_flashdata(
			'message',
			'정상적으로 삭제되었습니다'
		);
		$param =& $this->querystring;
		$redirecturl = admin_url($this->pagedir . '?' . $param->output());

		redirect($redirecturl);
	}


	/**
	 * 오래된 게시물변경로그 페이지입니다
	 */
	public function cleanlog()
	{
		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_board_posthistory_cleanlog';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		/**
		 * Validation 라이브러리를 가져옵니다
		 */
		$this->load->library('form_validation');

		/**
		 * 전송된 데이터의 유효성을 체크합니다
		 */
		$config = array(
			array(
				'field' => 'day',
				'label' => '기간',
				'rules' => 'trim|required|numeric|is_natural',
			),
		);
		$this->form_validation->set_rules($config);

		/**
		 * 유효성 검사를 하지 않는 경우, 또는 유효성 검사에 실패한 경우입니다.
		 * 즉 글쓰기나 수정 페이지를 보고 있는 경우입니다
		 */
		if ($this->form_validation->run() === false) {

			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['formrunfalse'] = Events::trigger('formrunfalse', $eventname);

		} else {
			/**
			 * 유효성 검사를 통과한 경우입니다.
			 * 즉 데이터의 insert 나 update 의 process 처리가 필요한 상황입니다
			 */

			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['formruntrue'] = Events::trigger('formruntrue', $eventname);

			if ($this->input->post('criterion') && $this->input->post('day')) {
				$deletewhere = array(
					'phi_datetime <=' => $this->input->post('criterion'),
				);
				$this->Post_history_model->delete_where($deletewhere);
				$view['view']['alert_message'] = '총 ' . number_format($this->input->post('log_count')) . ' 건의 ' . $this->input->post('day') . '일 이상된 게시물변경로그가 모두 삭제되었습니다';
			} else {
				$criterion = cdate('Y-m-d H:i:s', ctimestamp() - $this->input->post('day') * 24 * 60 * 60);
				$countwhere = array(
					'phi_datetime <=' => $criterion,
				);
				$log_count = $this->Post_history_model->count_by($countwhere);
				$view['view']['criterion'] = $criterion;
				$view['view']['day'] = $this->input->post('day');
				$view['view']['log_count'] = $log_count;
				if ($log_count > 0) {
					$view['view']['msg'] = '총 ' . number_format($log_count) . ' 건의 ' . $this->input->post('day') . '일 이상된 게시물변경로그가 발견되었습니다. 이를 모두 삭제하시겠습니까?';
				} else {
					$view['view']['alert_message'] = $this->input->post('day') . '일 이상된 게시물변경로그가 발견되지 않았습니다';
				}
			}
		}

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

		/**
		 * 어드민 레이아웃을 정의합니다
		 */
		$layoutconfig = array('layout' => 'layout', 'skin' => 'cleanlog');
		$view['layout'] = $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data = $view;
		$this->layout = element('layout_skin_file', element('layout', $view));
		$this->view = element('view_skin_file', element('layout', $view));
	}
}
