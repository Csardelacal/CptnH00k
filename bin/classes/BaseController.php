<?php

use auth\SSO;
use auth\SSOCache;
use auth\Token;
use spitfire\cache\MemcachedAdapter;
use spitfire\core\Environment;
use spitfire\io\session\Session;

/* 
 * The MIT License
 *
 * Copyright 2018 César de la Cal Bretschneider <cesar@magic3w.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

abstract class BaseController extends Controller
{
	
	/**
	 *
	 * @var SSO
	 */
	protected $sso;
	
	protected $user;
	
	/**
	 *
	 * @var auth\AppAuthentication
	 */
	protected $authapp;

	public function _onload() {
		/*
		 * Initialize the "primary SSO", this is the one that CptnH00k uses to
		 * authenticate it's users. Please note that, even though CptnH00k can 
		 * process hooks from several Authentication servers, it will only use the
		 * primary one to authenticate users.
		 * 
		 * Due to how SSO servers work, Cptnh00k can also not process webhooks between
		 * applications on different Auth Servers.
		 */
		$this->sso = new SSOCache(Environment::get('SSO'));
		
		$session  = Session::getInstance();
		
		/**
		 * 
		 * @var Token The token used to authenticate the user.
		 */
		$token      = $session->getUser();
		$memcache   = MemcachedAdapter::getInstance();
		
		/*
		 * Only system administrators are allowed to manage the data inside CptnH00k,
		 * therefore, when the user is logged in, we do check whether the user is
		 * part of any group that grants SysAdmin privileges.
		 * 
		 * Note: Generally speaking, CptnH00k is configured as a System application,
		 * therefore, only administrators can create sessions to it anyway. This is
		 * just an additional check to ensure that no user can manipulate this.
		 */
		$this->user = !$token? false : $memcache->get('cptnh00k_token_' . $token->getId(), function () use ($token) {
			if (!$token->isAuthenticated()) { return false; }
			
			$groups = $token->getTokenInfo()->groups;
			
			foreach ($groups as $id => $name) {
				if ($this->sso->getGroup($id)->sysadmin) { return $token->getTokenInfo()->user->id; }
			}
			
			return false;
		});
		
		/*
		 * When a signature is provided, the system needs to verify the given signature
		 * against the appropriate SSO server.
		 * 
		 * Currently, the system does not support having multiple applications on
		 * different SSO servers with the same app ID, this may never be a real
		 * use case, app ID are disposable random numbers that should have little
		 * room for collissions to be a real concern.
		 */
		if (isset($_GET['signature'])) {
			$pieces = explode(':', $_GET['signature']);
			
			if (!isset($pieces[1])) {
				throw new PublicException('Malformed signature', 400);
			}
			else {
				$credentials = db()->table('authapp')->get('appID', $pieces[1])->first(true);
				$secondarySSO  = new SSOCache($credentials->sso? $credentials->sso->ssoURL : $credentials->ssoURL);
				$this->authapp = $secondarySSO->authApp($_GET['signature']);
			}
		}
		
		$this->view->set('sso', $this->sso);
		$this->view->set('auth.user', $this->user);
		$this->view->set('auth.app',  $this->authapp);
	}
	
}