<?php
require __DIR__ . '/vendor/autoload.php';



/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient($obj)
{
    $client = new Google_Client();
    $client->setApplicationName('Google Sheets API PHP Quickstart');
    $client->setScopes(Google_Service_Sheets::SPREADSHEETS_READONLY);
    $client->setAuthConfig('credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');
    $client->setScopes(array('https://www.googleapis.com/auth/drive', 'https://www.googleapis.com/auth/spreadsheets.readonly'));

    $client->setAccessToken($obj);

    if ($client->isAccessTokenExpired()) {
        // Refresh the token if possible, else fetch a new one.
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            /*  printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';*/
            $authCode = trim($obj->acces_token);

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }
    }
    return $client;
}

function obtener_datos($obj)
{

    $client = getClient($obj);
    $service = new Google_Service_Sheets($client);


    $spreadsheetId = '1GZu4w8_NiJS8I1--C-N5O2dPoj_Bv-ojekMRDS2ToMQ';
    $range = 'A1:AC4115';
    $response = $service->spreadsheets_values->get($spreadsheetId, $range);
    $values = $response->getValues();


    $array_competencias_gs = [
        (object) ["id" => 540, "nombre" => "Wimbledon"],
        (object) ["id" => 560, "nombre" => "us-open"],
        (object) ["id" => 580, "nombre" => "australia-open"],
        (object) ["id" => 520, "nombre" => "roland-garros"]
    ];
    $array = [];
    foreach ($array_competencias_gs as $gs) {
        $array[$gs->id] = ["cantidad" => 0, "torneos" => []];
        // $jugador["torneo"][$gs->id] = 0;         
    }

    $array_datos_gs = [];
    $jugadores = [];

    $es_primero = true;
    foreach ($values as $value) {
        if ($es_primero) {
            $es_primero = false;
            continue;
        }
        foreach ($array_competencias_gs as $gs) {
            if (array_search($gs->id, $value)) {

                if (isset($array_datos_gs[(int)$value[3]]) && $gs->id === (int)$value[3]) {
                    array_push($array_datos_gs[$gs->id]["ediciones"], [
                        "id_jugador" => $value[18], "jugador_ganador" => $value[15], "anio_torneo" => $value[0], "mes_torneo" => $value[7], "dia_torneo" => $value[8], "superficie_torneo" => $value[12],
                        "condiciones" => $value[11],
                    ]);
                } else {
                    $guardar = [
                        "id" => (int)$value[3],
                        "nombre" => $value[2],
                        "ediciones" => [],
                        "ganadores" => [],
                    ];
                    $array_datos_gs[$gs->id] = $guardar;
                    array_push($array_datos_gs[$gs->id]["ediciones"], [
                        "id_jugador" => $value[18], "jugador_ganador" => $value[15], "anio_torneo" => $value[0], "mes_torneo" => $value[7], "dia_torneo" => $value[8], "superficie_torneo" => $value[12],
                        "condiciones" => $value[11],
                    ]);
                }
                if (!isset($jugadores[$value[18]])) {

                    $jugadores[$value[18]] = [
                        "id_jugador" => $value[18],
                        "nombre" => $value[15],
                        "torneo" => $array,
                    ];
                }
            } else {
                continue;
            }
        }
    }

    foreach ($array_datos_gs as $datos) {

        foreach ($datos["ediciones"] as $edicion) {

            $jugadores[$edicion["id_jugador"]]["torneo"][$datos["id"]]["cantidad"]++;
            array_push($jugadores[$edicion["id_jugador"]]["torneo"][$datos["id"]]["torneos"], $edicion);
        }
    }
    foreach ($jugadores as $jugador) {
        foreach ($array_competencias_gs as $gs) {

            if ((int)$jugador["torneo"][$gs->id]["cantidad"] !== 0) {
                array_push($array_datos_gs[$gs->id]["ganadores"], ["id_jugador" => $jugador["id_jugador"], "cantidad" => $jugador["torneo"][$gs->id]["cantidad"]]);
            }
        }
    }
   



    $cantidad = [];
    foreach ($array_datos_gs[520]["ganadores"] as $key => $value) {
        $cantidad[$key] = $value['cantidad'];
    }
    array_multisort($cantidad, SORT_DESC, $array_datos_gs[520]["ganadores"]);
    $cantidad = [];
    foreach ($array_datos_gs[540]["ganadores"] as $key => $value) {
        $cantidad[$key] = $value['cantidad'];
    }
    array_multisort($cantidad, SORT_DESC, $array_datos_gs[540]["ganadores"]);
    $cantidad = [];
    foreach ($array_datos_gs[560]["ganadores"] as $key => $value) {
        $cantidad[$key] = $value['cantidad'];
    }
    array_multisort($cantidad, SORT_DESC, $array_datos_gs[560]["ganadores"]);
    $cantidad = [];
    foreach ($array_datos_gs[580]["ganadores"] as $key => $value) {
        $cantidad[$key] = $value['cantidad'];
    }
    array_multisort($cantidad, SORT_DESC, $array_datos_gs[580]["ganadores"]);

    $respuesta = (object)[
        "array_datos_gs" => $array_datos_gs,
        "jugadores" => $jugadores,
    ];
    return $respuesta;
}
