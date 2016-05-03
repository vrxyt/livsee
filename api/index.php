<?php
error_reporting(E_ALL);ini_set('display_errors', 1);
// Disable caching so AJAX doesn't grab old data and set content type as JSON
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
header('Content-type: text/json');
 
// Get Request URI and break into components
$request = trim(filter_input(INPUT_SERVER, 'REQUEST_URI'), '/');
$uriVars = explode('/', $request, 4);
 
// Determine class, method, and parameters based on number of URI components
if (count($uriVars) === 4) {
    list ($root, $class, $method, $paramsStr) = $uriVars;
    $params = explode('/', $paramsStr);
} elseif (count($uriVars) === 3) {
    list ($root, $class, $method) = $uriVars;
    $params = !empty($_POST) ? $_POST : $_GET;
} else {
    die (json_encode(array('code' => 0, 'error' => 'Invalid URI')));
}
 
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
    die (json_encode(array('code' => 1, 'error' => "Class '$class' is not defined.")));
}

// Call class/method specified in URL
try {
    $obj = new $class($params);
    if (!method_exists($obj, $method)) {
        die (json_encode(array('code' => 2, 'error' => "Method '$method' for class '$class' is not defined.")));
    }
    $results = $obj->$method();
} catch (Exception $e) {
    die (json_encode(array('code' => 3, 'error' => $e->getMessage())));
}
 
// Encode results as JSON if not already
if (!$obj->isJson($results)) {
    $results = json_encode($results);
}

// Return JSON string
echo $results;