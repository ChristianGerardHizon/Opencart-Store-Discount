<?php
class ControllerExtensionModuleStoreDiscount extends Controller
{
    private $error = array();

    public function index()
    {

        $this->load->language('extension/module/store_discount');
        $this->load->model('setting/setting');

        // on save
        if ( ($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }


        // add cancel button
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);


		// breadcrumbs
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);
		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/store_discount', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/store_discount', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

        // build view
        $this->document->setTitle($this->language->get('heading_title'));
        $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('extension/module/store_discount', $data));
    }

    protected function validate()
	{

        if (!$this->user->hasPermission('modify', 'extension/module/store_discount')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;

    }

    public function install()
    {

        $this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "store_discount 
        (
            `store_discount_id` int(11) NOT NULL AUTO_INCREMENT,
            `store_id`          int(11) NOT NULL,
            `category_id`       int(11) NOT NULL,
            `is_percentage`     tinyint(1) NOT NULL,
            `customer_group_id` int(11) NOT NULL,
            `quantity`          int(4) NOT NULL DEFAULT 0,
            `priority`          int(5) NOT NULL DEFAULT 1,
            `price`             decimal(15,4) NOT NULL DEFAULT 0.0000,
            `date_start`        date NOT NULL DEFAULT '0000-00-00',
            `date_end`          date NOT NULL DEFAULT '0000-00-00',
            PRIMARY KEY (`store_discount_id`)
        )");

    }

    public function uninstall()
    {

        $this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('module_store_discount');

        $this->db->query("DROP TABLE " . DB_PREFIX . "store_discount");
    }
}