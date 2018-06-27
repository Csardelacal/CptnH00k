<?php

/**
 * Prebuilt test controller. Use this to test all the components built into
 * for right operation. This should be deleted whe using Spitfire.
 */

class HomeController extends BaseController
{
	
	/**
	 * 
	 */
	public function index() {
		
		if (!$this->user) {
			$this->response->setBody('Redirecting...')->getHeaders()->redirect(url('user', 'login'));
		}
		
		$apps = $this->sso->getAppList();
		$this->view->set('message', print_r($apps, true));
	}
}