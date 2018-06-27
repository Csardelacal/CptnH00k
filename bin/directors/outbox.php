<?php

use auth\SSO;
use spitfire\mvc\Director;

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

class OutboxDirector extends Director
{
	
	public function process() {
		
		console()->info('Started outbox processor')->ln();
		$pending = db()->table('outbox')->get('delivered', null)->where('scheduled', '<', time())->range(0, 20);
		
		foreach ($pending as $job) {
			
			if ($job->app->noSSOSupport()) {
				$querystring = [ 'psk' => $job->app->getPSK() ];
			}
			else {
				$sso = new SSO($job->app->sso? $job->app->sso->ssoURL : $job->app->ssoURL);
				$querystring = [
					'signature' => (string)$sso->makeSignature($job->app->sso? $job->app->appId : null),
					'psk' => $job->app->getPSK()
				];
			}
			
			$response = request($job->url . '?' . http_build_query($querystring))
			->header('Content-type', 'application/json')
			->post(json_encode(json_decode($job->payload)))
			->send();
			
			if ($response->status() !== 200 && $job->attempt < 10) {
				$retry = db()->table('outbox')->newRecord();
				$retry->app = $job->app;
				$retry->url = $job->url;
				$retry->payload = $job->payload;
				$retry->attempt = $job->attempt + 1;
				$retry->scheduled = time() + (300 * $job->attempt);
				$retry->store();
				
				console()->error('Failed delivering hook, received code ' . $response->status())->ln();
			}
			
			$job->response = sprintf("Code: %s\n%s", $response->status(), $response->html());
			$job->delivered = time();
			$job->store();
			
		}
	}
	
}