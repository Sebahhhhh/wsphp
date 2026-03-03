<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Allow-Headers: Content-Type, Accept");

if (($_SERVER["REQUEST_METHOD"] ?? "") === "OPTIONS") {
    http_response_code(204);
    exit;
}

$metodo = $_SERVER["REQUEST_METHOD"] ?? "GET";
$ct = strtolower($_SERVER["CONTENT_TYPE"] ?? "application/json");
$accept = strtolower($_SERVER["HTTP_ACCEPT"] ?? "application/json");

$isXmlIn = (strpos($ct, "xml") !== false);
$isXmlOut = (strpos($accept, "xml") !== false);

$body = file_get_contents("php://input");
$data = [];

if (!empty($body)) {
    if ($isXmlIn) {
        $xml = @simplexml_load_string($body);
        if ($xml !== false) {
            $data = json_decode(json_encode($xml), true) ?? [];
        }
    } else {
        $tmp = json_decode($body, true);
        if (is_array($tmp)) $data = $tmp;
    }
}

switch ($metodo) {
    case "GET":
        $response = ["metodo" => "GET", "nome" => "demo", "valore" => 1];
        break;

    case "POST":
        $data["metodo"] = "POST";
        $data["valore"] = (int)($data["valore"] ?? 0) + 2000;
        $response = $data;
        break;

    case "PUT":
        $data["metodo"] = "PUT";
        $data["valore"] = (int)($data["valore"] ?? 0) + 1;
        $response = $data;
        break;

    case "DELETE":
        $response = ["metodo" => "DELETE", "ok" => true];
        break;

    default:
        http_response_code(405);
        $response = ["errore" => "Metodo non supportato"];
        break;
}

if ($isXmlOut) {
    header("Content-Type: application/xml; charset=UTF-8");
    $xml = new SimpleXMLElement("<root/>");
    array_walk_recursive($response, function ($value, $key) use ($xml) {
        $xml->addChild($key, htmlspecialchars((string)$value));
    });
    echo $xml->asXML();
} else {
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode($response);
}

?>