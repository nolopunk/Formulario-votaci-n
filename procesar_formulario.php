<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Contacto</title>
    <link rel="stylesheet" href="styles.css">
</head>
 <!--script redireccionamineto -->
    <script>
        // Redirección después de cierto tiempo (en este caso, después de 3 segundos)
        setTimeout(function() {
        window.location.href = "index.php";
        }, 5000); // 3000 milisegundos = 3 segundos
    </script>
 <!--script contador seguridad html -->
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
    <?php
    // Validar y procesar el formulario

    // Verificar si el formulario se envió con el método POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Conexión a la base de datos
        $servername = "localhost";
        $username = "root";
        $password = "nolopunk";
        $dbname = "votacion_web";

        // Crear conexión
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Verificar conexión
        if ($conn->connect_error) {
                die("Conexión fallida: " . $conn->connect_error);
        }

        // Función para limpiar los datos de entrada
        function limpiarDatos($datos) {
            $datos = trim($datos);
            $datos = stripslashes($datos);
            $datos = htmlspecialchars($datos);
            return $datos;
        }

        // Validar RUT chileno
        function validarRut($rut) {
            $rut = preg_replace('/[^k0-9]/i', '', $rut);
            $rut = strtoupper($rut);
        if (is_numeric(substr($rut, 0, -1))) {
                $rut = substr($rut, 0, -1) . '-' . substr($rut, -1);
            }
            $partes = explode('-', $rut);
            $cuerpo = $partes[0];
            $digito = $partes[1];
            $suma = 0;
            $multiplo = 2;
        for ($i = strlen($cuerpo) - 1; $i >= 0; $i--) {
                $suma += $cuerpo[$i] * $multiplo;
            if ($multiplo < 7) {
                    $multiplo++;
            } else {
                $multiplo = 2;
                }
            }
                $resultado = 11 - ($suma % 11);
                $resultado = ($resultado == 11) ? 0 : (($resultado == 10) ? 'K' : $resultado);
                return $resultado == $digito;
            }         

        // Limpiar y validar datos del formulario
            $nombre = limpiarDatos($_POST["nombre"]);
            $alias = limpiarDatos($_POST["alias"]);
            $rut = limpiarDatos($_POST["rut"]);  
            $email = limpiarDatos($_POST["email"]);  
            $region = limpiarDatos($_POST["region"]);
            $comuna = limpiarDatos($_POST["comuna"]);   
            $candidato = limpiarDatos($_POST["candidato"]);
            $pregunta= limpiarDatos($_POST["opcion"]);

        // Validar correo electrónico
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                die("Formato de correo electrónico inválido");               
            }

        // Validar RUT chileno
        if (!validarRut($rut)) {
                die("Este RUT chileno es inválido");
            }

        // Insertar varios checkbox
            $opciones_seleccionadas = $_POST['opcion'];
            $pregunta = implode(',', $opciones_seleccionadas);

        // Preparar consulta SQL
            $sql = "INSERT INTO votantes (nombre, alias, rut, email, region, comuna, candidato, pregunta) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        // Preparar declaración
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssss",  $nombre, $alias, $rut, $email, $region, $comuna, $candidato, $pregunta);

        // Ejecutar consulta
        if ($stmt->execute()) {
                echo "Mensaje enviado correctamente";
                //header("Location: localhost/index.php");
            } else {
                echo "Error al enviar el mensaje: " . $conn->connect_error;
            }

        //echo "Conexión exitosa";

            // Cerrar declaración y conexión
            $stmt->close();
            $conn->close();
        } else {
        // Si el formulario no se envió por POST, redirigir a la página de formulario
            //header("Location: localhost/index.php");
            exit();
        }
    ?>
</body>
</html>