<?php

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

class AppController extends BaseController
{
	
	public function config() {
		
	}
	
	public function refresh() {
		
		$ssoList = db()->table('authapp')->get('isSSO', true)->all();
		
		if ($ssoList->isEmpty()) {
			/*
			 * Create the basic record, this allows the system to work with a single
			 * SSO provider from the get-go
			 */
			$record = db()->table('authapp')->newRecord();
			$record->appID = $this->sso->getAppId();
			$record->name = 'SSO Server';
			$record->sso = null;
			$record->isSSO = true;
			$record->ssoURL = spitfire\core\Environment::get('SSO');
			$record->configuration = null;
			$record->store();
			
			$ssoList = collect([$record]);
		}
		
		foreach ($ssoList as $ssoRecord) {
			if ($ssoRecord->appID == $this->sso->getAppId()) {
				$ssoRecord->ssoURL = spitfire\core\Environment::get('SSO');
				$ssoRecord->store();
			}
			
			$sso  = new \auth\SSO($ssoRecord->ssoURL);
			$apps = $sso->getAppList();
			
			foreach ($apps as $app) {
				if (db()->table('authapp')->get('appID', $app->id)->first()) { continue; }
				
				$record = db()->table('authapp')->get('appID', $app->id)->first()?: db()->table('authapp')->newRecord();
				$record->appID = $app->id;
				$record->name  = $app->name;
				$record->sso   = $ssoRecord;
				$record->isSSO = false;
				$record->configuration = null;
				$record->store();
			}
		}
		
		return $this->response->setBody('Okay');
		
	}
	
}