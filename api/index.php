<?php
/* API notes
 * 
 * Functions list:
 * 
 * /api/<api_key>/stream/info - returns all active stream information
 * /api/<api_key>/stream/ping - returns recording status for all current live channels
 * /api/<api_key>/stream/ping/<channelname> - returns stream live and recording status for a specific channel
 * /api/<api_key>/stream/record-start/<channelname> - starts recording the specified channel
 * /api/<api_key>/stream/record-stop/<channelname> - stops recording the specified channel
 * /api/<api_key>/subscription/add/<channelname> - Add current user (verified through API key) as a subscriber to specified channel
 * /api/<api_key>/subscription/remove/<channelname> - Remove current user (verified through API key) as a subsriber to specified channel
 * /api/<api_key>/subscription/list - Show list of all current subscriptions for current user (verified through API key)
 * 
 */
error_reporting(E_ALL);ini_set('display_errors', 1);
// Disable caching so AJAX doesn't grab old data and set content type as JSON
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
header('Content-type: text/json');
 
require_once '../lib/database.class.php';
require_once '../lib/user.class.php';
$user = new user();

// Get Request URI and break into components
$request = trim(filter_input(INPUT_SERVER, 'REQUEST_URI'), '/');
$uriVars = explode('/', $request, 5);
 
// Determine class, method, and parameters based on number of URI components
if (count($uriVars) === 5) {
    list ($root, $key, $class, $method, $paramsStr) = $uriVars;
    $params = explode('/', $paramsStr);
} elseif (count($uriVars) === 4) {
    list ($root, $key, $class, $method) = $uriVars;
    $params = !empty($_POST) ? $_POST : $_GET;
} else {
    die (json_encode(array('code' => 0, 'error' => 'Invalid URI')));
}
 
// verify the api key
if (!$user->verifyAPIkey($key)) { die (json_encode(array('code' => 1, 'error' => 'Invalid API Key'))); }

// Replace restricted keywords with valid function names
$transform = [
	'list' => '_list'
];
$method = str_replace(array_keys($transform), array_values($transform), $method);

// Replace hyphens (from URL) with underscores (used in methods) to allow prettier URLs
$method = str_replace('-', '_', $method);
 
// Auto-load classes
spl_autoload_register(function ($class) {
	if ($class !== 'index') {
		if ($class !== 'index' && file_exists(strtolower($class) . '.php')) {
			include strtolower($class) . '.php';
		} elseif (file_exists('../lib/' . strtolower($class) . '.class.php')) {
			include '../lib/' . strtolower($class) . '.class.php';
		}
	}
});
if (!class_exists($class)) {
    die (json_encode(array('code' => 2, 'error' => "Class '$class' is not defined.")));
}

// Call class/method specified in URL
try {
    $obj = new $class($key, $params);
    if (!method_exists($obj, $method)) {
        die (json_encode(array('code' => 3, 'error' => "Method '$method' for class '$class' is not defined.")));
    }
    $results = $obj->$method();
} catch (Exception $e) {
    die (json_encode(array('code' => 4, 'error' => $e->getMessage())));
}
 
// Encode results as JSON if not already
if (!$obj->isJson($results)) {
    $results = json_encode($results);
}

// Return JSON string
echo $results;