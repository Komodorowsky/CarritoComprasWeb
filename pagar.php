<?php
include 'global/config.php';
include 'global/conexion.php';
include 'carritos.php';
include 'templates/cabecera.php';

?>
<br>
<br>
<?php

if ($_POST) {
    $total = 0;
    $SID = session_id();
    $correo = $_POST['email'];

    foreach ($_SESSION['CARRITO'] as $indice => $producto) {
        $total = $total + ($producto['Precio'] * $producto['Cantidad']);
    }
    $sentancia = $pdo->prepare("INSERT INTO `tblventas` (`ID`, `Clavetransaccion`, `Paypaldatos`, `Fecha`, `Correo`, `Total`, `Status`) 
    VALUES (NULL, :Clavetransaccion, '', NOW() , :Correo, :Total, 'pendiente');");


    $sentancia->bindParam(":Clavetransaccion", $SID);
    $sentancia->bindParam(":Correo", $correo);
    $sentancia->bindParam(":Total", $total);
    $sentancia->execute();
    $idventa = $pdo->lastInsertId();

    foreach ($_SESSION['CARRITO'] as $indice => $producto) {

        $sentancia = $pdo->prepare("INSERT INTO `tbldetalleventa` (`ID`, `IDventa`, `IDproducto`, `Preciounitario`, `Cantidad`, `Descargado`) 
   VALUES (NULL, :IDventa, :IDproducto, :Preciounitario, :Cantidad, '0');");

        $sentancia->bindParam(":IDventa", $idventa);
        $sentancia->bindParam(":IDproducto", $producto['ID']);
        $sentancia->bindParam(":Preciounitario", $producto['Precio']);
        $sentancia->bindParam(":Cantidad", $producto['Cantidad']);
        $sentancia->execute();
    }

    // echo "<h3>" . $total . "<h3>";
}

?>

<script src="https://www.paypalobjects.com/api/checkout.js"></script>

<style>
    /* Media query for mobile viewport */
    @media screen and (max-width: 400px) {
        #paypal-button-container {
            width: 100%;
        }
    }

    /* Media query for desktop viewport */
    @media screen and (min-width: 400px) {
        #paypal-button-container {
            width: 250px;
            display: inline-block;
        }
    }
</style>

<div class="jumbotron text-center">
    <h1 class="display-4">¡Paso Final!</h1>
    <hr class="my-4">
    <p class="lead">Estas a punto de pagar con paypal la cantidad de:
    <h4>$<?php echo number_format($total, 2) ?></h4>

    <div id="paypal-button-container"></div>
    </p>

    <p>Los productos podran ser descargados una vez que se procese el pago</p><br>
    <strong>(Para aclaraciones: mgworkoficial@gmail.com)</strong>
</div>





<script>
    paypal.Button.render({
        env: 'sandbox', // sandbox | production
        style: {
            label: 'checkout', // checkout | credit | pay | buynow | generic
            size: 'responsive', // small | medium | large | responsive
            shape: 'pill', // pill | rect
            color: 'gold' // gold | blue | silver | black
        },

        // PayPal Client IDs - replace with your own
        // Create a PayPal app: https://developer.paypal.com/developer/applications/create

        client: {
            sandbox: 'AX9-H44BQxH7sQ8UhgmI4x5m0lyeBZJLw5w_ttTLrBazuW2Yg54anlIYILUjkMR-st1MPS2vS63P4hzt',
            production: ''
        },

        // Wait for the PayPal button to be clicked

        payment: function(data, actions) {
            return actions.payment.create({
                payment: {
                    transactions: [{
                        amount: {
                            total: '<?php echo $total; ?>',
                            currency: 'MXN',
                        },
                        description: "Compra de productos: $<?php echo number_format($total, 2); ?>",
                        custom: "<?php echo $SID; ?>#<?php echo openssl_encrypt($idventa, COD, KEY); ?>"
                    }]
                }
            });
        },


        // Wait for the payment to be authorized by the customer

        onAuthorize: function(data, actions) {
            return actions.payment.execute().then(function() {
                console.log(data);
                window.location = "verificador.php?paymentToken=" + data.paymentToken + "&paymentID=" + data.paymentID;
            });
        }

    }, '#paypal-button-container');
</script>

<?php

include 'templates/pie.php';

?>