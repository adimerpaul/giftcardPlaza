<!DOCTYPE html>
<html lang="en">
    <head>
       <?php
            echo view('header.php');
       ?>
        <?php foreach($css_files as $file): ?>
            <link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
        <?php endforeach; ?>
        <?php foreach($js_files as $file): ?>
            <script src="<?php echo $file; ?>"></script>            
        <?php endforeach; ?>
    </head>
    <body class="sb-nav-fixed">
        <?php
            echo view('menu.php');
        ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Usuarios</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Lista de Usuarios</li>
                        </ol>                                           
                        <div class="card mb-4">                          
                            <div class="card-body">
                                <?php echo $output; ?>                                
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
