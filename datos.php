<?php
require_once "funciones.php";
/*
  listar todos los posts o solo uno
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

  if (isset($_GET["id_token"]) && isset($_GET["access_token"]) && isset($_GET["expires_in"]) && isset($_GET["login_hint"]) && isset($_GET["scope"]) && isset($_GET["token_type"]) && isset($_GET["expires_at"])) {
    $id_token = $_GET["id_token"];
    $access_token = $_GET["access_token"];
    $expires_in = $_GET["expires_in"];
    $login_hint = $_GET["login_hint"];
    $scope = $_GET["scope"];
    $token_type = $_GET["token_type"];
    $expires_at = $_GET["expires_at"];
    $obj = [
      "access_token" => $access_token,
      "expires_in" => $expires_in,
      "refresh_token" => $login_hint,
      "scope" => $scope,
      "token_type" => $token_type,
      "created" => $expires_at,
    ];

    $client = new Google_Client(['client_id' => "379180781445-ois6l06tjii66f37vds8etp24tnoi22r.apps.googleusercontent.com"]);  // Specify the CLIENT_ID of the app that accesses the backend
    $payload = $client->verifyIdToken($id_token);
    if ($payload) {

      $datos =   obtener_datos($obj);
      header("HTTP/1.1 200 OK");
      echo json_encode((object)["exito"=>true,"datos"=>$datos]);
      exit();
    } else {
      header("HTTP/1.1 200 OK");
      echo json_encode((object)["exito"=>false,"mensaje"=>"Error al validar Tokens Google Api"]);
      exit();
    }
  }else{
    header("HTTP/1.1 200 OK");
    echo json_encode((object)["exito"=>false,"mensaje"=>"Faltan parametros para poder hacer la consulta"]);
    exit();
  }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
}


if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
}


if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
}

header("HTTP/1.1 400 Bad Request");