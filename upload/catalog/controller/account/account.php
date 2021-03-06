<?php
namespace Opencart\Application\Controller\Account;
class Account extends \Opencart\System\Engine\Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/account', 'language=' . $this->config->get('config_language'));

			$this->response->redirect($this->url->link('account/login', 'language=' . $this->config->get('config_language')));
		}

		$this->load->language('account/account');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'language=' . $this->config->get('config_language'))
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', 'language=' . $this->config->get('config_language'))
		];

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['edit'] = $this->url->link('account/edit', 'language=' . $this->config->get('config_language'));
		$data['password'] = $this->url->link('account/password', 'language=' . $this->config->get('config_language'));
		$data['address'] = $this->url->link('account/address', 'language=' . $this->config->get('config_language'));

		$data['credit_cards'] = [];

		$files = glob(DIR_APPLICATION . 'controller/extension/credit_card/*.php');

		foreach ($files as $file) {
			$code = basename($file, '.php');

			if ($this->config->get('payment_' . $code . '_status') && $this->config->get('payment_' . $code . '_card')) {
				$this->load->language('extension/credit_card/' . $code, 'extension');

				$data['credit_cards'][] = [
					'name' => $this->language->get('extension')->get('heading_title'),
					'href' => $this->url->link('extension/credit_card/' . $code, 'language=' . $this->config->get('config_language'))
				];
			}
		}

		$data['wishlist'] = $this->url->link('account/wishlist', 'language=' . $this->config->get('config_language'));
		$data['order'] = $this->url->link('account/order', 'language=' . $this->config->get('config_language'));
		$data['download'] = $this->url->link('account/download', 'language=' . $this->config->get('config_language'));

		if ($this->config->get('total_reward_status')) {
			$data['reward'] = $this->url->link('account/reward', 'language=' . $this->config->get('config_language'));
		} else {
			$data['reward'] = '';
		}

		$data['return'] = $this->url->link('account/returns', 'language=' . $this->config->get('config_language'));
		$data['transaction'] = $this->url->link('account/transaction', 'language=' . $this->config->get('config_language'));
		$data['newsletter'] = $this->url->link('account/newsletter', 'language=' . $this->config->get('config_language'));
		$data['recurring'] = $this->url->link('account/recurring', 'language=' . $this->config->get('config_language'));

		if ($this->config->get('config_affiliate_status')) {
			$this->load->model('account/affiliate');

			$affiliate_info = $this->model_account_affiliate->getAffiliate($this->customer->getId());

			if (!$affiliate_info) {
				$data['affiliate'] = $this->url->link('account/affiliate/add', 'language=' . $this->config->get('config_language'));
			} else {
				$data['affiliate'] = $this->url->link('account/affiliate/edit', 'language=' . $this->config->get('config_language'));
			}

			if ($affiliate_info) {
				$data['tracking'] = $this->url->link('account/tracking', 'language=' . $this->config->get('config_language'));
			} else {
				$data['tracking'] = '';
			}
		} else {
			$data['affiliate'] = false;
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/account', $data));
	}
}
