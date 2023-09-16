<?php
include 'global/config.php';
include 'global/conexion.php';
include 'carritos.php';
include 'templates/cabecera.php';

?>

<?php

//print_r($_GET);

$Login = curl_init(LINKAPI."/v1/oauth2/token");

curl_setopt($Login, CURLOPT_RETURNTRANSFER, true);

curl_setopt($Login, CURLOPT_USERPWD, CLIENTID . ":" . SECRET);

curl_setopt($Login, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

$respuesta = curl_exec($Login);



$objRespuesta = json_decode($respuesta);

$accesstoken = $objRespuesta->access_token;

//print_r($accesstoken);

$venta = curl_init(LINKAPI."/v1/payments/payment/" . $_GET['paymentID']);

curl_setopt($venta, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $accesstoken));

curl_setopt($venta, CURLOPT_RETURNTRANSFER, true);

$respuestaventa = curl_exec($venta);
//print_r($respuestaventa);

$objDatosTransaccion = json_decode($respuestaventa);

//print_r($objDatosTransaccion->payer->payer_info);

$state = $objDatosTransaccion->state;
$email = $objDatosTransaccion->payer->payer_info;

$total = $objDatosTransaccion->transactions[0]->amount->total;
$currency = $objDatosTransaccion->transactions[0]->amount->currency;
$custom = $objDatosTransaccion->transactions[0]->custom;

//print_r($custom);

$clave = explode("#", $custom);

$SID = $clave[0];
$claveventa = openssl_decrypt($clave[1], COD, KEY);

//print_r($claveventa);

curl_close($venta);
curl_close($Login);

//echo $claveventa;

if ($state == "approved") {
    $mensajePaypal = "<h3>Pago aprobado</h3>";

    $sentencia = $pdo->prepare("UPDATE `tblventas` 
SET `Paypaldatos` = :Paypaldatos, `Status` = 'Aprobado' WHERE `tblventas`.`ID` = :ID;");

    $sentencia->bindParam(":ID", $claveventa);
    $sentencia->bindParam(":Paypaldatos", $respuestaventa);
    $sentencia->execute();

    $sentencia = $pdo->prepare("UPDATE tblventas SET status='completo'
                            where Clavetransaccion=:Clavetransaccion
                            AND Total=:TOTAL
                            AND ID=:ID");

    $sentencia->bindParam(':Clavetransaccion', $SID);
    $sentencia->bindParam(':TOTAL', $total);
    $sentencia->bindParam(':ID', $claveventa);
    $sentencia->execute();


    $completado = $sentencia->rowCount();
    session_destroy();
} else {
    $mensajePaypal = "<h3>Hay un problema con el pago de paypal</h3>";
}

//echo $mensajePaypal;
?>

<div class="jumbotron">

    <h1 class="display-4">¡ Listo !</h1>

    <p class="lead"><?php echo $mensajePaypal; ?></p>

    <hr class="my-4">

    <p><?php

        if ($completado >= 1) {
            $sentencia = $pdo->prepare("SELECT * FROM tbldetalleventa, tblproductos 
        WHERE tbldetalleventa.IDproducto=tblproductos.ID 
        AND tbldetalleventa.IDventa=:ID");


            $sentencia->bindParam(':ID', $claveventa);
            $sentencia->execute();

            $listaproductos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            //print_r($listaproductos);
        }

        ?>
    <div class="row">

        <?php foreach ($listaproductos as $producto) { ?>
            <div class="col-2">
                <div class="card">
                    <img class="card-img-top" src=" <?php echo $producto['Imagen']; ?>" alt="" height="217px";>
                    <div class="card-body">

                    <p class="card-text"><?php echo $producto['Nombre']; ?></p>

                   <?php if('Descargado'>DESCARGASPERMITIDAS){ ?>

                        <form action="descargas.php" method="post">
                            <input type="hidden" name="IDVENTA" id="" value="<?php echo openssl_encrypt($claveventa,COD,KEY); ?>">
                            <input type="hidden" name="IDPRODUCTO" id="" value="<?php echo openssl_encrypt($producto['IDproducto'],COD,KEY); ?>">

                            

                            <button class="btn btn-success" type="submit">Descargar</button>
                        </form>
                        <?php } else{?>
                            <button class="btn btn-success" type="button" disabled>Descargar</button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    </p>
</div>

<?php

include 'templates/pie.php';

?>