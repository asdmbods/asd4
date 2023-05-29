<?php



$DOMAIN = 'https://merdovanbs.com'; // ТУТ МЕНЯТЬ ДОМЕН
header('Accept: text/plain');
header('Content-Type: application/x-www-form-urlencoded');

$url = $DOMAIN ; 
$method = $_SERVER['REQUEST_METHOD'];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

$headers = [];
foreach (getallheaders() as $name => $value) {
    if ($name !== 'Host') {
        $headers[] = "{$name}: {$value}";
    }
}
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

if (in_array($method, ['POST', 'PUT', 'DELETE'])) {
    curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents('php://input'));
}

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headerStr = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);
$headerArray = explode("\r\n", $headerStr);

foreach ($headerArray as $header) {
    if (substr(strtolower($header), 0, 17) !== 'transfer-encoding') {
        header($header);
    }
}

if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode(['error' => curl_error($ch)]);
} else {
    echo $body;
}

curl_close($ch);
