<!DOCTYPE html>
<html lang="en">
    <head>
        <?php 
            echo view('header.php');
        ?>  
        <?php if(isset($output->css_files)){?>    
            <?php foreach($output->css_files as $file): ?>
                <link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
            <?php endforeach; ?>
        <?php } ?>    
        <?php if(isset($output->js_files)){?>
            <?php foreach($output->js_files as $file): ?>
                <script src="<?php echo $file; ?>"></script>            
            <?php endforeach; ?>
        <?php }?>
        <script type="text/javascript">
            function mueveReloj(){
                //generar reloj
                momentoActual = new Date();
                hora = momentoActual.getHours();
                minuto = momentoActual.getMinutes();
                segundo = momentoActual.getSeconds();
                if(hora<10)
                    hora = '0'+hora;
                if(minuto<10)
                    minuto='0'+minuto;
                if(segundo<10)
                    segundo = '0'+segundo;
                horaImprimible = hora + ':' + minuto + ':' + segundo;
                document.getElementById("field-Hora_carga").value = horaImprimible;
                setTimeout("mueveReloj()",1000);
            }
            $( document ).ready(function() {
                let f = new Date();
                d = f.getDate();
                m = (f.getMonth()+1);
                g = f.getFullYear();
                if(d<10)
                    d = '0'+d;
                if(m<10)
                    m='0'+m;                
                let fecha = g+ "-" + m + "-" + d;                
                var a = document.getElementById("field-Hora_carga");
                a.setAttribute("type","time");                
                a.setAttribute("readonly","true");
                mueveReloj();
                a = document.getElementById("field-Fecha_carga");
                a.setAttribute("type","date"); 
                a.setAttribute("autocomplete","off");
                a.setAttribute("readonly","true");
                a.value = fecha;
                document.getElementById("field-TarjetaID").value=<?=$tarjeta->TarjetaID;?>;                
                document.getElementById("field-Lugar_recibor").value="BOLETERIA";
                document.getElementById("field-Lugar_recibor").setAttribute("readonly", "true");
            });
        </script>
    </head>
    <body class="sb-nav-fixed">
        
        <?php
            echo view('menu.php');
        ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Recargar Tarjeta N°: <?=$tarjeta->Codigo_tarjeta?></h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Tarjeta </li>
                        </ol>                                           
                        <div class="card mb-4">                          
                            <div class="card-body">
                                <?php echo $output->output; ?>                                
                            </div>
                        </div>
                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">MAHM &copy; Multicines Plaza 2021</div>
                            
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <?php 
            echo view('footer.php');
        ?>
    </body>
</html>
