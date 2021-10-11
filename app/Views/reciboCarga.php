<!DOCTYPE html>
<html lang="en">
    <head>
        <?php 
            echo view('header.php');
        ?>
        <style>
            *{
                font-size: 150%;                
            }
            body{
                padding: 30px;
            }
        </style>
    </head>
    <body >
        <div class="row">
            <div class="col-12 text-center">
                <h1>MULTICINES PLAZA S.R.L.</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center">
                <h5>Av. Tacna, Jaén y Tomas Frias<br>
                    Teléfonos: 252-81290<br>
                    ORURO - BOLIVIA
                </h5>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center">
                <h2>CARGA DE CREDITO
                    Nro.<?=$id_tarjeta?>
                </h2>
                <h2>
                    Codigo Tarjeta:<?=$codigo_tarjeta?>
                </h2>
            </div>
        </div>        
        <div class="row">
            <div class="col-2"></div>
            <div class="col-8 text-left">
                <hr>
                <h5>
                    Fecha: <?=$fecha?><br>
                    Hora: <?=$hora?><br>
                    Señor(a): <?=$nombre_cliente?><br>
                    C.I.: <?=$ci?><br>
                    Lugar de Cobro:<?=$lugar?> <br>
                    Monto:  <?=$monto?> Bs. <br>
                    Porcentaje Beneficio:<?=$porcentaje?> %<br>
                    Monto total:  <?=$monto_total?>Bs.<br>
                </h5>
                <hr>
            </div>
            <div class="col-2"></div>
        </div>
       
        <div class="row">
            <div class="col-2"></div>
            <div class="col-8 text-left">
                <h5>
                    Usuario: <?=$usuario?>
                </h5>
            </div>
            <div class="col-2"></div>
        </div>

        <?php 
            echo view('footer.php');
        ?>
    </body>
</html>
