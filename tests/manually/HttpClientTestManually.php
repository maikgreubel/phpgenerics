<?php
set_include_path(get_include_path() . PATH_SEPARATOR . '../../');

require_once 'vendor/autoload.php';

use Generics\Client\HttpClient;
use Generics\Socket\Url;

$http = new HttpClient(new Url('httpbin.org', 80));
$http->request('GET');

if ($http->getResponseCode() == 200) {
    $response = "";

    while ($http->getPayload()->ready()) {
        $response = $http->getPayload()->read(
            $http->getPayload()->count()
        );
    }

    foreach ($http->getHeaders() as $headerName => $headerValue) {
        printf("%s: %s\n", $headerName, $headerValue);
    }
    printf("Response: %s\n", $response);

    $http->disconnect();
}
