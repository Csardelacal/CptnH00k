<?php


class HomeController extends BaseController
{
	
	/**
	 * 
	 */
	public function index() {
		
		if (!$this->user) {
			$this->response->setBody('Redirecting...')->getHeaders()->redirect(url('user', 'login'));
		}
		
		$apps = db()->table('authapp')->getAll()->all();
		$this->view->set('apps', $apps);
	}
}
