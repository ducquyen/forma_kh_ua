<?php
class ControllerModuleFilter extends Controller {
	private $error = array(); 
	
	public function index() {
		$this->load->language('module/filter');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		$this->load->model('catalog/filter');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('filter', $this->request->post);		
			
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}
				
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_content_top'] = $this->language->get('text_content_top');
		$this->data['text_content_bottom'] = $this->language->get('text_content_bottom');		
		$this->data['text_column_left'] = $this->language->get('text_column_left');
		$this->data['text_column_right'] = $this->language->get('text_column_right');
		$this->data['text_export'] = $this->language->get('text_export');
		
		$this->data['fiter_view_style'] = $this->language->get('fiter_view_style');
		$this->data['filter_default'] = $this->language->get('filter_default');
		$this->data['filter_select'] = $this->language->get('filter_select');
		$this->data['filter_accordion'] = $this->language->get('filter_accordion');
		$this->data['filter_spoiler'] = $this->language->get('filter_spoiler');
		
		$this->data['entry_layout'] = $this->language->get('entry_layout');
		$this->data['entry_position'] = $this->language->get('entry_position');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_add_module'] = $this->language->get('button_add_module');
		$this->data['button_remove'] = $this->language->get('button_remove');
		$this->data['button_create'] = $this->language->get('button_create');
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		if (isset($this->session->data['error'])) {
			$this->data['error'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} else {
			$this->data['error'] = '';
		}
		
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/filter', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/filter', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['create'] = $this->url->link('module/filter/create', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['is_installed'] = $this->model_catalog_filter->showTable('category_option');
		
		// Стиль фильтра
		$this->data['fiter_view_styles'] = array();
		
		$this->data['fiter_view_styles'][] = array(
			'fiter_view_style' => 'filter.tpl',
			'title'    => $this->language->get('filter_default'),
		);
		
		$this->data['fiter_view_styles'][] = array(
			'fiter_view_style' => 'filter2.tpl',
			'title'    => $this->language->get('filter_select'),
		);
		
		$this->data['fiter_view_styles'][] = array(
			'fiter_view_style' => 'filter3.tpl',
			'title'    => $this->language->get('filter_accordion'),
		);
		
		$this->data['fiter_view_styles'][] = array(
			'fiter_view_style' => 'filter4.tpl',
			'title'    => $this->language->get('filter_spoiler'),
		);
		
		if (isset($this->request->post['filter_style'])) {
			$this->data['filter_style'] = $this->request->post['filter_style'];
		} else {
			$this->data['filter_style'] = $this->config->get('filter_style');
		}
		
		$this->data['modules'] = array();
		
		if (isset($this->request->post['filter_module'])) {
			$this->data['modules'] = $this->request->post['filter_module'];
		} elseif ($this->config->get('filter_module')) { 
			$this->data['modules'] = $this->config->get('filter_module');
		}				
				
		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();

		$this->template = 'module/filter.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	public function create() {
		$this->load->model('catalog/filter');
		if ($this->model_catalog_filter->showTable('category_option')) {
		  $this->session->data['error'] = 'Таблицы уже существуют';
		} else {
		  $this->model_catalog_filter->createTables();
		  $this->session->data['success'] = 'Создание таблиц прошло успешно';
		}
		$this->redirect(HTTPS_SERVER . 'index.php?route=module/filter&token=' . $this->session->data['token']);
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/filter')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (isset($this->request->post['latest_module'])) {
			foreach ($this->request->post['latest_module'] as $key => $value) {
				if (!$value['image_width'] || !$value['image_height']) {
					$this->error['image'][$key] = $this->language->get('error_image');
				}
			}
		}		
				
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>