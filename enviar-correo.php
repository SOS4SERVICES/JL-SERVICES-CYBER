<?php

header('Content-Type: application/json; charset=UTF-8');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';


/* =============================================
   SOLO PERMITIR SOLICITUDES POST
============================================= */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido.'
    ]);

    exit;
}


/* =============================================
   OBTENER DATOS
============================================= */

$nombre = trim($_POST['nombre'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$email = trim($_POST['email'] ?? '');
$mensaje = trim($_POST['mensaje'] ?? '');

$website = trim($_POST['website'] ?? '');


/* =============================================
   HONEYPOT ANTI-SPAM
============================================= */

if ($website !== '') {

    echo json_encode([
        'success' => true,
        'message' => 'Mensaje enviado correctamente.'
    ]);

    exit;
}


/* =============================================
   VALIDACIONES
============================================= */

if ($nombre === '' || $email === '' || $mensaje === '') {

    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'Completa todos los campos obligatorios.'
    ]);

    exit;
}


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'El correo electrónico no es válido.'
    ]);

    exit;
}


if (mb_strlen($nombre) > 100) {

    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'El nombre es demasiado largo.'
    ]);

    exit;
}


if (mb_strlen($telefono) > 30) {

    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'El teléfono no es válido.'
    ]);

    exit;
}


if (mb_strlen($mensaje) > 5000) {

    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => 'El mensaje es demasiado largo.'
    ]);

    exit;
}


/* =============================================
   LIMPIAR DATOS
============================================= */

$nombreHtml = htmlspecialchars(
    $nombre,
    ENT_QUOTES,
    'UTF-8'
);

$emailHtml = htmlspecialchars(
    $email,
    ENT_QUOTES,
    'UTF-8'
);

$telefonoHtml = htmlspecialchars(
    $telefono,
    ENT_QUOTES,
    'UTF-8'
);

$mensajeHtml = nl2br(
    htmlspecialchars(
        $mensaje,
        ENT_QUOTES,
        'UTF-8'
    )
);


/* =============================================
   CREAR PHPMailer
============================================= */

$mail = new PHPMailer(true);


try {

    /* =========================================
       SMTP HOSTINGER
    ========================================= */

    $mail->isSMTP();
   
    $mail->Host = 'smtp.hostinger.com';

    $mail->SMTPAuth = true;

    $mail->Username =
        'gerencia.comercial369@sos4services.com.mx';

    $mail->Password =
        'Comercial@SOS4#26';

    $mail->SMTPSecure =
        PHPMailer::ENCRYPTION_SMTPS;

    $mail->Port = 465;


    /* =========================================
       CODIFICACIÓN
    ========================================= */

    $mail->CharSet = 'UTF-8';

    $mail->Encoding = 'base64';


    /* =========================================
       REMITENTE
    ========================================= */

    $mail->setFrom(
        'gerencia.comercial369@sos4services.com.mx',
        'Gerencia Comercial - SOS4 SERVICES'
    );


    /* =========================================
       DESTINATARIO
    ========================================= */

    $mail->addAddress(
        'gerencia.comercial369@sos4services.com.mx',
        'Gerencia Comercial - SOS4 SERVICES'
    );


    /* =========================================
       RESPONDER AL CLIENTE
    ========================================= */

    $mail->addReplyTo(
        $email,
        $nombre
    );


    /* =========================================
       ASUNTO
    ========================================= */

    $mail->Subject =
        'Nueva solicitud web - ' . $nombre;


    /* =========================================
       HTML
    ========================================= */

    $mail->isHTML(true);


    $mail->Body = '

    <!DOCTYPE html>

    <html lang="es">

    <head>

        <meta charset="UTF-8">

    </head>

    <body
        style="
            margin:0;
            padding:0;
            background:#f2f2f2;
            font-family:Arial, Helvetica, sans-serif;
        "
    >

        <table
            width="100%"
            cellpadding="0"
            cellspacing="0"
            style="
                background:#f2f2f2;
                padding:30px 10px;
            "
        >

            <tr>

                <td align="center">

                    <table
                        width="600"
                        cellpadding="0"
                        cellspacing="0"
                        style="
                            max-width:600px;
                            width:100%;
                            background:#ffffff;
                            border-radius:12px;
                            overflow:hidden;
                        "
                    >

                        <tr>

                            <td
                                style="
                                    background:#8f2500;
                                    padding:28px;
                                    color:#ffffff;
                                "
                            >

                                <h1
                                    style="
                                        margin:0;
                                        font-size:25px;
                                    "
                                >
                                    SOS4 SERVICES
                                </h1>

                                <p
                                    style="
                                        margin:8px 0 0;
                                    "
                                >
                                    Nueva solicitud desde el sitio web
                                </p>

                            </td>

                        </tr>


                        <tr>

                            <td
                                style="
                                    padding:30px;
                                    color:#333333;
                                "
                            >

                                <h2
                                    style="
                                        color:#8f2500;
                                        margin-top:0;
                                    "
                                >
                                    Información del cliente
                                </h2>


                                <p>
                                    <strong>Nombre:</strong><br>
                                    ' . $nombreHtml . '
                                </p>


                                <p>
                                    <strong>Correo:</strong><br>

                                    <a
                                        href="mailto:' . $emailHtml . '"
                                        style="color:#ff5a00;"
                                    >
                                        ' . $emailHtml . '
                                    </a>

                                </p>


                                <p>
                                    <strong>Teléfono:</strong><br>
                                    ' .
                                    (
                                        $telefonoHtml !== ''
                                        ? $telefonoHtml
                                        : 'No proporcionado'
                                    )
                                    . '
                                </p>


                                <hr
                                    style="
                                        border:0;
                                        border-top:1px solid #dddddd;
                                        margin:25px 0;
                                    "
                                >


                                <h3
                                    style="
                                        color:#8f2500;
                                    "
                                >
                                    Mensaje
                                </h3>


                                <div
                                    style="
                                        background:#f7f7f7;
                                        border-left:4px solid #ff5a00;
                                        padding:18px;
                                        border-radius:5px;
                                        line-height:1.6;
                                    "
                                >

                                    ' . $mensajeHtml . '

                                </div>

                            </td>

                        </tr>


                        <tr>

                            <td
                                style="
                                    background:#222222;
                                    padding:20px;
                                    text-align:center;
                                    color:#cccccc;
                                    font-size:12px;
                                "
                            >

                                Mensaje recibido desde
                                <strong style="color:#ffffff;">
                                    sos4services.com.mx
                                </strong>

                                <br><br>

                                SOS4 SERVICES

                                <br>

                                Seguridad • Ingeniería • Soluciones

                            </td>

                        </tr>

                    </table>

                </td>

            </tr>

        </table>

    </body>

    </html>

    ';


    /* =========================================
       TEXTO PLANO
    ========================================= */

    $mail->AltBody =

        "NUEVA SOLICITUD - SOS4 SERVICES\n\n" .

        "Nombre: " . $nombre . "\n" .

        "Correo: " . $email . "\n" .

        "Teléfono: " .
        (
            $telefono !== ''
            ? $telefono
            : 'No proporcionado'
        ) .
        "\n\n" .

        "Mensaje:\n" .
        $mensaje;


    /* =========================================
       ENVIAR
    ========================================= */

    $mail->send();


    echo json_encode([
        'success' => true,
        'message' => 'Mensaje enviado correctamente.'
    ]);


} catch (Exception $e) {

    error_log(
        'PHPMailer SOS4 SERVICES: ' .
        $mail->ErrorInfo
    );

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' =>
            'No fue posible enviar el mensaje. Intenta nuevamente.'
    ]);

}