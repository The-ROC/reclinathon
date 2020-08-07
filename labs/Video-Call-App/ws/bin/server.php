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

$serverName = 'ec2-52-91-202-91.compute-1.amazonaws.com';
if (property_exists($_SERVER, 'SERVER_NAME')) {
    $serverName = $_SERVER['SERVER_NAME'];
}
echo "\nStarting server ($serverName)... \n";
$cwd = getcwd();
echo "Working directory ($cwd)\n";
// Run the server application through the WebSocket protocol on port 8080
$app = new App($serverName, 8080, '0.0.0.0'); //App(hostname, port, 'whoCanConnectIP', '')

echo "Created app \n";
//create socket routes
//route(uri, classInstance, arrOfAllowedOrigins)
$app->route('/comm', new Comm, ['*']);

echo "Created route \n";
//run websocket
$app->run();
echo "Running... \n";