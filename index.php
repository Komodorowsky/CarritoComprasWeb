<?php
include 'global/config.php';
include 'global/conexion.php';
include 'carritos.php';
include 'templates/cabecera.php';

?>



    <br>
    <br>
    <br>

    <?php if($mensaje!=""){ ?>
    <div class="container">
        <br>
        <div class="alert alert-success" role="alert">
    <?php echo $mensaje; ?>
    <a href="mostrarCarrito.php" class="badge badge-success">Ver carrito</a>

    <?php } ?>
</div>

        <div class="row">

            <?php
            $sentencia = $pdo->prepare("SELECT * FROM `tblproductos`");
            $sentencia->execute();
            $listaproductos = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            //print_r($listaproductos);
            ?>

            <?php foreach ($listaproductos as $productos) { ?>

                <div class="col-3">
                    <div class="card">
                        <img title="<?php echo $productos['Nombre']; ?>"
                         alt="<?php echo $productos['Nombre']; ?>" 
                         class="card-img-top" 
                         src="<?php echo $productos['Imagen']; ?>" 
                         data-toggle="popover" 
                         data-trigger="hover" 
                         data-content="<?php echo $productos['Descripcion']; ?>" 
                         data-title="<?php echo $productos['Nombre']; ?>"
                         height="317px";
                         >
                         

                        <div class="card-body">
                            <h5 class="card-title">$<?php echo $productos['Precio']; ?></h5>
                            <p class="card-text">Descripcion</p>

                            <form method="post" action="">

                                <input type="hidden" name="id" id="id" value="<?php echo openssl_encrypt($productos['ID'],COD,KEY); ?>">
                                <input type="hidden" name="name" id="name" value="<?php echo openssl_encrypt($productos['Nombre'],COD,KEY); ?>">
                                <input type="hidden" name="precio" id="precio" value="<?php echo openssl_encrypt($productos['Precio'],COD,KEY); ?>">
                                <input type="hidden" name="cantidad" id="cantidad" value="<?php echo openssl_encrypt(1,COD,KEY); ?>">

                               <button class="btn btn-primary" name="btnaction" value="Agregar" type="submit">Agregar al carrito</button>
                       
                            </form>

                        </div>
                    </div>
                </div>



            <?php } ?>

        </div>
    </div>
    <script>

        $(function() {
            $('[data-toggle="popover"]').popover()
        });

    </script>


<?php

include 'templates/pie.php';

?>