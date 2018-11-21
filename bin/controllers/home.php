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
		
		$stats = [];
		$span  = 86400;
		$step  = 20 * 60; //Resolution of the graph is 10 minutes
		$max   = 0;
		
		for ($i = 0; $i < ($span / $step); $i++) {
			$in  = db()->table('inbox')->get('processed', time() - ($i + 1) * $step, '>')->where('processed', '<', time() - $i * $step)->count();
			$out = db()->table('outbox')->get('delivered', time() - ($i + 1) * $step, '>')->where('delivered', '<', time() - $i * $step)->count();
			$max = max($max, $in + $out);
			
			array_unshift($stats, ['in' => $in, 'out' => $out]);
		}
		
		$this->view->set('stats', $stats);
		$this->view->set('max', max(1, $max));
	}
}
