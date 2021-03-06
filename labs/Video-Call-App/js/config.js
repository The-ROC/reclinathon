/**
 * @author Amir Sanni <amirsanni@gmail.com>
 * @date 23-Dec-2016
 */

'use strict';

let loc = window.location;
let baseUrl = loc.protocol + '//' + loc.host + '/';
let splitPath = loc.pathname.split('/');
for (let i=0; i<splitPath.length; i++) {
    // Skip empty entry @ beginning and page name @ end
    if (i == 0 || i == splitPath.length-1) { continue; }

    baseUrl += splitPath[i] + '/';
}

const appRoot = baseUrl;
const wsUrl = 'ws://localhost:8080';//use wss://localhost:8080/comm for secured connection
const wsUrl2 = 'ws://10.0.0.235:8080';
const spinnerClass = 'fa fa-spinner faa-spin animated';