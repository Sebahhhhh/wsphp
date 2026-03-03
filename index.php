<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Accept");

$metodo = $_SERVER["REQUEST_METHOD"];
$contentType = $_SERVER["CONTENT_TYPE"] ?? "application/json";
$accept = $_SERVER["HTTP_ACCEPT"] ?? "application/json";

// Legge i dati in input
$body = file_get_contents('php://input');
$data = [];

if (!empty($body)) {
    if (strpos($contentType, 'xml') !== false) {
        $xml = simplexml_load_string($body);
        $data = json_decode(json_encode($xml), true);
    } else {
        $data = json_decode($body, true) ?? [];
    }
}

// Funzione per rispondere
function respond($data, $accept) {
    if (strpos($accept, 'xml') !== false) {
        header("Content-Type: application/xml");
        $xml = new SimpleXMLElement('<root/>');
        array_walk_recursive($data, array($xml, 'addChild'));
        echo $xml->asXML();
    } else {
        header("Content-Type: application/json");
        echo json_encode($data);
    }
}

if ($metodo == "GET") {
    respond(["metodo" => "GET", "messaggio" => "Recuperato con successo"], $accept);
}
elseif ($metodo == "POST") {
    $data["metodo"] = "POST";
    $data["valore"] = ($data["valore"] ?? 0) + 2000;
    respond($data, $accept);
}
elseif ($metodo == "PUT") {
    $data["metodo"] = "PUT";
    $data["valore"] = ($data["valore"] ?? 0) * 2;
    respond($data, $accept);
}
elseif ($metodo == "DELETE") {
    $data["metodo"] = "DELETE";
    $data["eliminato"] = true;
    respond($data, $accept);
}
?>