<?php

use spitfire\core\Environment;

/*
 * Creates a test environment that can be used to store configuration that affects
 * the behavior of an application.
 */
$e = new Environment('test');
$e->set('db', 'mysqlpdo://root:root@localhost:3306/cptnh00k?encoding=utf8&prefix=test_');
$e->set('SSO', 'http://1054451631:OEjx3tkr6LpjGngm32hNZAk8JDOfovHrDPBD2XwD2Yw@localhost/Auth/'); 

$e->set('debugging_mode', true);
$e->set('debug_mode', true);