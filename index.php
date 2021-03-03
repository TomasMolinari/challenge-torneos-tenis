<!DOCTYPE html>

<HTML LANG="Spanish">

<head>
    <meta charset="utf-8">
    <title>Prueba</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>

<body>

    <div id="ingresar" class="d-flex justify-content-around">

        <div class="col-md-4 text-center">
            <p>Es necesario ingresar con GMAIL antes de continuar </p>
            <!--Add buttons to initiate auth sequence and sign out-->
            <button id="authorize_button" style="display: none;">Authorize</button>
            <button id="signout_button" style="display: none;">Sign Out</button>

            <pre id="content" style="white-space: pre-wrap;"></pre>
        </div>
    </div>
    <script type="text/javascript">
        // Client ID and API key from the Developer Console
        var CLIENT_ID = '379180781445-ois6l06tjii66f37vds8etp24tnoi22r.apps.googleusercontent.com';
        var API_KEY = 'AIzaSyB_xQRWtXcWhiqEl482EqzQWJl-2vz2v2w';

        // Array of API discovery doc URLs for APIs used by the quickstart
        var DISCOVERY_DOCS = ["https://sheets.googleapis.com/$discovery/rest?version=v4"];

        // Authorization scopes required by the API; multiple scopes can be
        // included, separated by spaces.
        var SCOPES = "https://www.googleapis.com/auth/spreadsheets.readonly";

        var authorizeButton = document.getElementById('authorize_button');
        var signoutButton = document.getElementById('signout_button');

        /**
         *  On load, called to load the auth2 library and API client library.
         */
        function handleClientLoad() {
            gapi.load('client:auth2', initClient);
        }

        /**
         *  Initializes the API client library and sets up sign-in state
         *  listeners.
         */
        function initClient() {
            gapi.client.init({
                apiKey: API_KEY,
                clientId: CLIENT_ID,
                discoveryDocs: DISCOVERY_DOCS,
                scope: SCOPES
            }).then(function() {
                // Listen for sign-in state changes.
                gapi.auth2.getAuthInstance().isSignedIn.listen(updateSigninStatus);

                // Handle the initial sign-in state.


                updateSigninStatus(gapi.auth2.getAuthInstance().isSignedIn.get());

                authorizeButton.onclick = handleAuthClick;
                signoutButton.onclick = handleSignoutClick;
            }, function(error) {
                appendPre(JSON.stringify(error, null, 2));
            });
        }

        /**
         *  Called when the signed in status changes, to update the UI
         *  appropriately. After a sign-in, the API is called.
         */
        function updateSigninStatus(isSignedIn) {
            if (isSignedIn) {
                authorizeButton.style.display = 'none';
                signoutButton.style.display = 'block';
                var id_token = gapi.auth2.getAuthInstance().currentUser.get().getAuthResponse().id_token;
                var access_token = gapi.auth2.getAuthInstance().currentUser.get().getAuthResponse().access_token;
                var expires_in = gapi.auth2.getAuthInstance().currentUser.get().getAuthResponse().expires_in;
                var login_hint = gapi.auth2.getAuthInstance().currentUser.get().getAuthResponse().login_hint;
                var scope = gapi.auth2.getAuthInstance().currentUser.get().getAuthResponse().scope;
                var token_type = gapi.auth2.getAuthInstance().currentUser.get().getAuthResponse().token_type;
                var expires_at = gapi.auth2.getAuthInstance().currentUser.get().getAuthResponse().expires_at;
                $(".contenedor_cargando").removeClass("invisible");

                httpGetAsync("datos.php/?id_token=" + id_token + "&access_token=" + access_token + "&expires_in=" + expires_in + "&login_hint=" + login_hint + "&scope=" + scope + "&token_type=" + token_type + "&expires_at=" + expires_at, function(a) {
                    var datos = JSON.parse(a);
                    if(datos.exito == true){
                        mostrar_datos(datos.datos);
                    }else{
                        $(".contenedor_cargando").addClass("invisible");
                        alert(datos.mensaje);
                        console.log(datos.mensaje);
                    }
                    
                });



                //listMajors();
            } else {
                authorizeButton.style.display = 'block';
                signoutButton.style.display = 'none';
            }
        }

        /**
         *  Sign in the user upon button click.
         */
        function handleAuthClick(event) {

            gapi.auth2.getAuthInstance().signIn();

        }

        /**
         *  Sign out the user upon button click.
         */
        function handleSignoutClick(event) {
            gapi.auth2.getAuthInstance().signOut();
        }

        /**
         * Append a pre element to the body containing the given message
         * as its text node. Used to display the results of the API call.
         *
         * @param {string} message Text to be placed in pre element.
         */
        function appendPre(message) {
            var pre = document.getElementById('content');
            var textContent = document.createTextNode(message + '\n');
            pre.appendChild(textContent);
        }

        /**
         * Print the names and majors of students in a sample spreadsheet:
         * https://docs.google.com/spreadsheets/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit
         */
        function listMajors() {
            gapi.client.sheets.spreadsheets.values.get({
                spreadsheetId: '1GZu4w8_NiJS8I1--C-N5O2dPoj_Bv-ojekMRDS2ToMQ',
                range: 'A1:AC4115',
            }).then(function(response) {
                var range = response.result;
                if (range.values.length > 0) {


                } else {
                    appendPre('No data found.');
                }
            }, function(response) {
                appendPre('Error: ' + response.result.error.message);
            });
        }

        function httpGetAsync(theUrl, callback) {
            var xmlHttp = new XMLHttpRequest();
            xmlHttp.onreadystatechange = function() {
                if (xmlHttp.readyState == 4 && xmlHttp.status == 200)
                    callback(xmlHttp.responseText);
            }
            xmlHttp.open("GET", theUrl, true); // true for asynchronous 
            xmlHttp.send(null);
        }

        function mostrar_datos(datos) {
            
            $(".contenedor_cargando").addClass("invisible");
            $("#cuerpo").removeClass("invisible");
            $.each(datos.array_datos_gs, function(i, val) {

                $("#" + val.id).find("#nombre_competencia").html(val.nombre);
                var id_jugador = val.ganadores[0]["id_jugador"];
                $("#" + val.id).find("#nombre_jugador").html(datos.jugadores[id_jugador]["nombre"]);
                $("#" + val.id).find("#cantidad_jugador").html(val.ganadores[0]["cantidad"]);


                var cantidad = datos.jugadores[id_jugador]["torneo"][val.id]["torneos"].length - 1;
                var datos_torneo = datos.jugadores[id_jugador]["torneo"][val.id]["torneos"][cantidad];
                $("#" + val.id).find("#nombre_jugador").attr("data-content", "Ultimo Ganado <br> Fecha :" + datos_torneo["dia_torneo"] + "/" + datos_torneo["mes_torneo"] + "/" + datos_torneo["anio_torneo"] + "<br>  " + "Superficie:" + datos_torneo["superficie_torneo"] + "<br>  " + "Condicion:" + datos_torneo["condiciones"]);

            });

        }
    </script>
    <script>
        // jQuery
        $(document).ready(function() {
            $('[data-toggle="popover"]').popover();
        });
    </script>
    <script async defer src="https://apis.google.com/js/api.js" onload="this.onload=function(){};handleClientLoad()" onreadystatechange="if (this.readyState === 'complete') this.onload()">
    </script>

    </div>
    <div id="cuerpo" class="invisible">
        <div class="row">
            <div class="col-md-12 text-center">
                <h3>Grand Slams</h3>
            </div>
        </div>
        <div class="d-flex justify-content-around">
            <div id="520" class="col-md-3 contenedor-datos ">
                <div class="row text-center">
                    <div class="col-md-12">
                        <label id="nombre_competencia"></label>
                    </div>
                </div>
                <div class="row text-center">
                    <div class="col-md-6">
                        <label id="nombre_jugador" data-html="true" data-toggle="popover" data-trigger="click" data-content="  " data-placement="bottom"></label>
                    </div>
                    <div class="col-md-6">
                        <label id="cantidad_jugador"></label>
                    </div>
                </div>

            </div>
            <div id="540" class="col-md-3 contenedor-datos">
                <div class="row text-center">
                    <div class="col-md-12">
                        <label id="nombre_competencia"></label>
                    </div>
                </div>
                <div class="row text-center">
                    <div class="col-md-6">
                        <label id="nombre_jugador" data-html="true" data-toggle="popover" data-trigger="click" data-content="  " data-placement="bottom"></label>
                    </div>
                    <div class="col-md-6">
                        <label id="cantidad_jugador"></label>
                    </div>
                </div>

            </div>
            <div id="560" class="col-md-3 contenedor-datos">
                <div class="row text-center">
                    <div class="col-md-12">
                        <label id="nombre_competencia"></label>
                    </div>
                </div>
                <div class="row text-center">
                    <div class="col-md-6">
                        <label id="nombre_jugador" data-html="true" data-toggle="popover" data-trigger="click" data-content="  " data-placement="bottom"></label>
                    </div>
                    <div class="col-md-6">
                        <label id="cantidad_jugador"></label>
                    </div>
                </div>

            </div>
            <div id="580" class="col-md-3 contenedor-datos">
                <div class="row text-center">
                    <div class="col-md-12">
                        <label id="nombre_competencia"></label>
                    </div>
                </div>
                <div class="row text-center">
                    <div class="col-md-6">
                        <label id="nombre_jugador" data-html="true" data-toggle="popover" data-trigger="click" data-content="  " data-placement="bottom"></label>
                    </div>
                    <div class="col-md-6">
                        <label id="cantidad_jugador"></label>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="contenedor_cargando invisible d-flex justify-content-around">
        <img src='cargando.gif' class="img-fluid">
    </div>
</body>

</HTML>