<?php
include 'global/config.php';
include 'global/conexion.php';
include 'carritos.php';

?>

<?php

print_r($_POST);

if($_POST){
    $IDVenta = openssl_decrypt($_POST['IDVENTA'], COD, KEY); // Corrected the variable name from 'IDventa' to 'IDVENTA'
    $IDProducto = openssl_decrypt($_POST['IDPRODUCTO'], COD, KEY); // Corrected the variable name from 'IDproducto' to 'IDPRODUCTO'

    $sentencia = $pdo->prepare("SELECT * FROM tbldetalleventa 
                            WHERE IDventa=:IDventa
                            AND IDproducto=:IDproducto
                            AND Descargado<".DESCARGASPERMITIDAS);

    $sentencia->bindParam(':IDventa', $IDVenta); // Corrected the variable name from 'SID' to 'IDVenta'
    $sentencia->bindParam(':IDproducto', $IDProducto); // Corrected the variable name from 'total' to 'IDProducto'

    $sentencia->execute();

    $listaProductos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    print_r($listaProductos);

    if ($sentencia->rowCount() > 0) {

        echo "<br><br><br><br><br><h2>Archivo en descarga</h2>";

        $nombreArchivos="archivos/".$listaProductos[0]['IDproducto'].".pdf";

        $nuevonombrearchivo=$_POST['IDVENTA'].$_POST['IDPRODUCTO'].".pdf";

        echo $nuevonombrearchivo;

        header("Content-Transfer-Encoding: binary");
        header("Content-type: application/force-download");
        header("Content-Disposition: attachment; filename=$nuevonombrearchivo");
        readfile("$nombreArchivos");    
        
        $sentencia = $pdo->prepare("UPDATE tbldetalleventa SET Descargado=Descargado+1
                                 WHERE IDventa=:IDventa AND IDproducto=:IDproducto");

        // Corrected parameter names in the bindParam calls
        $sentencia->bindParam(":IDventa", $IDVenta);
        $sentencia->bindParam(":IDproducto", $IDProducto);

        $sentencia->execute();
    } else {
        include 'templates/cabecera.php';

        echo "<br><br><br><br><br><h2>Tus descargas se agotaron</h2>";

        include 'templates/pie.php';
    }
}
?>

