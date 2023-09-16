<?php
include 'global/config.php';

include 'carritos.php';
include 'templates/cabecera.php';
?>

<br>
<h3 class="text-center">Lista del carrito</h3>

<?php if (!empty($_SESSION['CARRITO'])) { ?>
    <table class="table table-light table-bordered">
        <tbody>
            <tr>
                <th width="40%" class="text-center">Descripcion</th>
                <th width="15%" class="text-center">Cantidad</th>
                <th width="20%" class="text-center">Precio</th>
                <th width="20%" class="text-center">Total</th>
                <th width="5%">--</th>
            </tr>
            <?php $total = 0; ?>
            <?php foreach ($_SESSION['CARRITO'] as $indice => $producto) { ?>
                <tr>

                    <td width="40%" class="text-center"><?php echo $producto['Nombre']  ?></td>
                    <td width="15%" class="text-center"><?php echo $producto['Cantidad']  ?></td>
                    <td width="20%" class="text-center"><?php echo $producto['Precio']  ?></td>
                    <td width="20%" class="text-center"><?php echo number_format($producto['Cantidad'] * $producto['Precio'], 2)  ?></td>
                    <td width="5%" class="text-center">

                        <form action="" method="post">

                        <input type="hidden" 
                        name="id" 
                        value="<?php echo openssl_encrypt($producto['ID'],COD,KEY); ?>">
                                
                            <button class="btn btn-danger" type="submit" name="btnaction" value="Eliminar">Eliminar
                            </button>
                        </form>
                    </td>

                </tr>
                <?php $total = $total + ($producto['Cantidad'] * $producto['Precio']); ?>
            <?php } ?>
            <tr>
                <td colspan="3" align="right">
                    <h3>total</h3>
                </td>
                <td align="right">
                    <h3>$<?php
                            echo number_format($total, 2);
                            ?></h3>
                </td>

            </tr>

                <tr>
                    <td colspan="5">

                    <form action="pagar.php" method="post">
                        <div class="alert alert-success" role="alert">
                        <div class="form-group">
                            <label for="my-input">Correo de contacto:</label>
                            <input id="email"
                             name="email"
                             class="form-control"
                             type="email"
                             placeholder="por favor escribe tu correo"
                             require>
                        </div>

                        <small id="emailhelp"
                        class="form-text text-muted">
                        Los productos se enviaran a este correo</small>
                        </div>
                       <button class="btn btn-primary btn-lg btn-block" type="submit" value="proceder" name="btnaction">
                        Proceder a pagar
                       </button>
                        </form>
                    </td>
                </tr>

        </tbody>
    </table>
<?php } else { ?>
    <div class="alert alert-success">
        No hay productos
    </div>
<?php } ?>
<?php

include 'templates/pie.php';

?>