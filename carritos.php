<?php
session_start();
$mensaje = "";

if (isset($_POST['btnaction'])) {

    switch ($_POST['btnaction']) {
        case 'Agregar':

            if (is_numeric(openssl_decrypt($_POST['id'], COD, KEY))) {
                $ID = openssl_decrypt($_POST['id'], COD, KEY);
                $mensaje .= "OK ID CORRECTO" . $ID . "<br>";
            } else {
                $mensaje .= "ID INCORRECTO" . $ID . "<br>";
            }

            if (is_string(openssl_decrypt($_POST['name'], COD, KEY))) {
                $Nombre = openssl_decrypt($_POST['name'], COD, KEY);
                $mensaje .= "OK Nombre CORRECTO" . $Nombre . "<br>";
            } else {
                $mensaje .= "Algo mal con el nombre" . "<br>";
                break;
            }

            if (is_numeric(openssl_decrypt($_POST['cantidad'], COD, KEY))) {
                $cantidad = openssl_decrypt($_POST['cantidad'], COD, KEY);
                $mensaje .= "OK Cantidad CORRECTA" . $cantidad . "<br>";
            } else {
                $mensaje .= "Algo mal con la cantidad" . "<br>";
                break;
            }

            if (is_numeric(openssl_decrypt($_POST['precio'], COD, KEY))) {
                $Precio = openssl_decrypt($_POST['precio'], COD, KEY);
                $mensaje .= "OK Precio CORRECTO" . $Precio . "<br>";
            } else {
                $mensaje .= "Algo mal con el Precio" . "<br>";
                break;
            }

            if (!isset($_SESSION['CARRITO'])) {
                $_SESSION['CARRITO'] = array(); // Inicializa el carrito como un array vacío si no existe
            }

            if (isset($_SESSION['CARRITO'])) {
                $idProductos = array_column($_SESSION['CARRITO'], 'ID');

                if (in_array($ID, $idProductos)) {
                    $mensaje = "El producto ya ha sido seleccionado";
                } else {
                    $producto = array(
                        'ID' => $ID,
                        'Nombre' => $Nombre,
                        'Cantidad' => $cantidad,
                        'Precio' => $Precio,
                    );
                    $_SESSION['CARRITO'][] = $producto; // Agrega el producto al carrito
                    $mensaje = "Producto agregado...";
                }
            } else {
                $idproductos = array_column($_SESSION['CARRITO'], "ID");

                if (in_array($ID, $idproductos)) {
                    echo "<script>alert('El producto ya ha sido seleccionado')</script>";
                    $mensaje = "";
                } else {
                    $numeroproductos = count($_SESSION['CARRITO']);
                    $producto = array(
                        'ID' => $ID,
                        'Nombre' => $Nombre,
                        'Cantidad' => $cantidad,
                        'Precio' => $Precio,
                    );
                    $_SESSION['CARRITO'][$numeroproductos] = $producto;
                    $mensaje = "Producto agregado...";
                }
            }

            break;

        case "Eliminar":
            if (is_numeric(openssl_decrypt($_POST['id'], COD, KEY))) {
                $ID = openssl_decrypt($_POST['id'], COD, KEY);

                foreach ($_SESSION['CARRITO'] as $indice => $producto) {
                    if ($producto['ID'] == $ID) {
                        unset($_SESSION['CARRITO'][$indice]);
                        $mensaje = "Elemento borrado...";
                        break;
                    }
                }
            } else {
                $mensaje .= "ID INCORRECTO" . $ID . "<br>";
            }

            break;
    }
}
?>
