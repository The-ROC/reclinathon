<?php

/**
 * Description of server
 *
 * @author Amir <amirsanni@gmail.com>
 * @date 23-Dec-2016
 */

require 'vendor/autoload.php';

use Amir\Comm;
use Ratchet\App;

//set an array of origins allowed to connect to this server
//$allowed_origins = ['localhost', '127.0.0.1', '10.0.0.194'];

echo 'Starting server... \n';
// Run the server application through the WebSocket protocol on port 8080
$app = new App('localhost', 8080, '0.0.0.0'); //App(hostname, port, 'whoCanConnectIP', '')

echo 'Created app <br />';
//create socket routes
//route(uri, classInstance, arrOfAllowedOrigins)
$app->route('/comm', new Comm, ['*']);

echo 'Created route <br />';
//run websocket
$app->run();
echo 'Running... <br />';