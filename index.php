<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Votación</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<!-- script contador seguridad html -->
    <script>
    // Configuración avanzada de seguridad
    (function() {
        // Configurar Content Security Policy (CSP)
        var cspHeader = "default-src 'self'; script-src 'self' https://ajax.googleapis.com; style-src 'self' https://maxcdn.bootstrapcdn.com; img-src 'self' data:; font-src 'self' https://maxcdn.bootstrapcdn.com; object-src 'none'";
        document.querySelector('meta[http-equiv="Content-Security-Policy"]').setAttribute('content', cspHeader);

        // Configurar X-Frame-Options para prevenir clickjacking
        document.querySelector('meta[http-equiv="X-Frame-Options"]').setAttribute('content', 'DENY');

        // Configurar X-XSS-Protection para prevenir ataques XSS
        document.querySelector('meta[http-equiv="X-XSS-Protection"]').setAttribute('content', '1; mode=block');

        // Configurar X-Content-Type-Options para prevenir sniffing de tipo MIME
        document.querySelector('meta[http-equiv="X-Content-Type-Options"]').setAttribute('content', 'nosniff');

        // Protección contra ataques de CSRF
        function generateRandomToken(length) {
            var charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            var token = "";
            for (var i = 0; i < length; i++) {
                token += charset.charAt(Math.floor(Math.random() * charset.length));
            }
            return token;
        }

        var csrfToken = generateRandomToken(32);
        document.cookie = "csrf_token=" + csrfToken + "; Secure; HttpOnly; SameSite=Strict";

        // Agregar el token CSRF a todas las solicitudes AJAX
        var originalXHR = XMLHttpRequest.prototype.open;
        XMLHttpRequest.prototype.open = function() {
            if (arguments[0].toUpperCase() !== "GET") {
                this.setRequestHeader("X-CSRF-Token", csrfToken);
            }
            originalXHR.apply(this, arguments);
        };

        // Configuración de encabezados HTTP adicionales
        document.addEventListener("readystatechange", function() {
            if (document.readyState === "interactive" || document.readyState === "complete") {
                var headers = {
                    "Referrer-Policy": "no-referrer",
                    "Strict-Transport-Security": "max-age=31536000; includeSubDomains; preload",
                    "Feature-Policy": "camera 'none'; geolocation 'none'; microphone 'none'"
                };

                for (var header in headers) {
                    document.querySelector('meta[http-equiv="' + header + '"]').setAttribute('content', headers[header]);
                }
            }
        });
    })();
    </script>  
<body>
    <section class="container">
    <h1 class="font-weight-bold mb-3">Formularios de Votación</h1>              
    <!-- Formulario de contacto -->
    <form action="procesar_formulario.php" method="post">
        <div class="form-row mb-2">
            <div class="form-group col-md-6">
                <label for="nombre" class="font-weight-bold">Nombres y Apellidos : <span class="text-danger">*</span></label>
                <input type="text" class="form-control" placeholder="Ingresa tu Nombres y apellidos" id="nombre" name="nombre" required>
            </div>
            <div class="form-group col-md-6">
                <label for="alias" class="font-weight-bold">Alias : <span class="text-danger">*</span></label>
                <input type="text" class="form-control" placeholder="Tu Alias" id="alias" name="alias" required>
            </div>
            <div class="form-group col-md-6">
                <label for="rut" class="font-weight-bold">Rut :<span class="text-danger">*</span></label>
                <input type="text" class="form-control" placeholder="Tu Rut sin puntos ni guión" id="rut" name="rut" required>
            </div>
            <div class="form-group col-md-6">
                <label for="email" class="font-weight-bold">Correo electrónico : <span class="text-danger">*</span></label>
                <input type="email" class="form-control" placeholder="Tu Correo Electrónico" id="email" name="email" required>
            </div>  
            <div class="form-group col-md-6">
                <label for="region" class="font-weight-bold">Región :<span class="text-danger">*</span></label>
                <select class="texto-color" id="region" name="region" required>
                <?php
                // Conexión a la base de datos
                $servername = "localhost";
                $username = "root";
                $password = "nolopunk";
                $dbname = "votacion_web";

                $conn = new mysqli($servername, $username, $password, $dbname);
                if ($conn->connect_error) {
                    die("Conexión fallida: " . $conn->connect_error);
                }

                // Consulta SQL para obtener los datos
                $sql = "SELECT Region FROM datosChile";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Imprimir datos dentro de opciones del select
                    while($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row["Region"] . "'>" . $row["Region"] . "</option>";
                    }
                } else {
                    echo "<option value=''>No hay opciones disponibles</option>";
                }
                // Cerrar conexión
                $conn->close();
                ?>
                    <!-- Php listbox  -->
                <?php
                // Procesamiento del formulario
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                  if (isset($_POST["region"])) {
                    $opcion_seleccionada = $_POST["region"];
                    echo "<p>Has seleccionado: $opcion_seleccionada</p>";
                  } else {
                    echo "<p>No se ha seleccionado ninguna opción.</p>";
                  }
                }
                ?>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="comuna" class="font-weight-bold">Comuna :<span class="text-danger">*</span></label>
                <select class="texto-color" id="comuna" name="comuna" required>
                <?php
                // Conexión a la base de datos
                $servername = "localhost";
                $username = "root";
                $password = "nolopunk";
                $dbname = "votacion_web";

                $conn = new mysqli($servername, $username, $password, $dbname);
                if ($conn->connect_error) {
                    die("Conexión fallida: " . $conn->connect_error);
                }

                // Consulta SQL para obtener los datos
                $sql = "SELECT Comuna FROM datosChile";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Imprimir datos dentro de opciones del select
                    while($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row["Comuna"] . "'>" . $row["Comuna"] . "</option>";
                    }
                } else {
                    echo "<option value=''>No hay opciones disponibles</option>";
                }
                // Cerrar conexión
                $conn->close();
                ?>
                <!-- Php listbox  -->
                <?php
                // Procesamiento del formulario
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                  if (isset($_POST["region"])) {
                    $opcion_seleccionada = $_POST["region"];
                    echo "<p>Has seleccionado: $opcion_seleccionada</p>";
                  } else {
                    echo "<p>No se ha seleccionado ninguna opción.</p>";
                  }
                }
                ?>                 
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="candidato" class="font-weight-bold">Candidato :<span class="text-danger">*</span></label>
                <select class="texto-color" id="candidato" name="candidato" required>
                <?php
                // Opciones generadas dinámicamente usando PHP
                    $opciones = array("....", "Candidato 1", "Candidato 2", "Candidato 3");
                    foreach ($opciones as $opcion) {
                        echo "<option value='$opcion'>$opcion</option>";
                    }
                ?>
                <!-- Php listbox  -->
                <?php
                // Procesamiento del formulario
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                  if (isset($_POST["candidato"])) {
                    $opcion_seleccionada = $_POST["candidato"];
                    echo "<p>Has seleccionado: $opcion_seleccionada</p>";
                  } else {
                    echo "<p>No se ha seleccionado ninguna opción.</p>";
                  }
                }
                ?>                 
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="checkbox" class="font-weight-bold">Cómo se enteró de nosotros :<span class="text-danger">*</span></label>
                <input type="checkbox" id="opcion" name="opcion[]" value="web" /><label for="cbox1">Web</label>
                <input type="checkbox" id="opcion" name="opcion[]" value="tv" /><label for="cbox2">Tv</label>
                <input type="checkbox" id="opcion" name="opcion[]" value="redes sociales" /><label for="cbox3">Redes Sociales</label>
                <input type="checkbox" id="opcion" name="opcion[]" value="amigos" /><label for="cbox4">Amigos</label>
            </div>                                                                                    
        </div>
            <button type="submit" class="">Enviar</button>
    </form>
</section>
</body>
</html>
