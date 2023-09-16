<?php
    //0. REQUIRIENDO ARCHIVO DE CONEXION 
    require '../config/database.php';

    //1. BASE DE DATOS
    $db = conectarDB();

    //2. CAPTURANDO VARIABLES POR SUPERGLOBAL $_GET
    $email = $_GET['email'];
    $nombre = $_GET['nombre'];
    $nombre_depto = $_GET['nombre_depto'];
    $nombreEmpleado = $_GET['nombreEmpleado'];
    $ticket = $_GET['ticket'];
    $esNuevoCliente = $_GET['clienteExiste'];
    $msg_autom_encabezado = "Cordial saludo, señor(a): ";
    $nombreEmpresa = "CONSULTORA S.A.S";
    //----------------------------------------------
    $msg_autom_cuerpo1= "Gracias por confiar en ";
    $msg_autom_cuerpo2= ", su Solicitud ha sido recibida y se ha abierto un ticket con id número: ";
    $msg_autom_cuerpo3= " desde el departamento de ";
    $msg_autom_cuerpo4= " y será atendido por el(la) asesor(a) ";
    //----------------------------------------------
    $msg_clienteNuevo1= ", Bienvenid@ a ";
    $msg_clienteNuevo2= ", usted acaba de ser registrado en nuestra Base de Datos ";
    //----------------------------------------------
    $msg_clienteGeneral= "Hola sr(a) ";
    $msg_clienteAntiguo2= ", es un gusto tenerlo aqui nuevamente en ";

    //3. CONSULTAR ASIGNACIONES SEGUN ID DEL CLIENTE 
    $consulta_asignaciones = "SELECT DISTINCT  asig.`id asignacion` AS `ticket`, asig.`fecha` AS `fecha creacion`, dep.`nombre depto`, asig.`nombreEmpleado`
    FROM asignaciones AS asig
    LEFT JOIN departamento AS dep
    ON asig.`iddepto` =  dep.`iddepto`
    LEFT JOIN empleados AS emp
    ON dep.`iddepto` =  emp.`iddepto`
    WHERE asig.`email` = '$email' ORDER BY asig.`id asignacion` DESC;";

    $result_consulta_asignaciones = mysqli_query($db, $consulta_asignaciones);
    $solicitudes = array();

    //4. GUARDANDO ARRAY CONSULTA ASIGNACIONES SEGUN ID DEL CLIENTE 
    while($rows = mysqli_fetch_array($result_consulta_asignaciones)):
    $solicitudes[] = $rows;
    endwhile;


    //REQUIRIENDO EL CODIGO html DEL header---------------
    require '../includes/header.php';

?>




<!-- 5. MENSAJE CONDICIONAL DE BIENVENIDA: CLIENTE NUEVO Y CLIENTE ANTIGUO -->
    <?php if( $esNuevoCliente === "0"):?>
        <h1 class="encabezado-principal msg-cliente-nuevo">
            <?php echo 
                $msg_clienteGeneral . '<span class="style-cliente">' . $nombre . '</span>' .
                $msg_clienteNuevo1 . '<span class="style-empresa">' . $nombreEmpresa . '</span>' .
                $msg_clienteNuevo2; ?>
        </h1>
    <?php endif;?>
    <?php if( $esNuevoCliente === "1"):?>
        <h1 class="encabezado-principal msg-cliente-antiguo">
            <?php echo 
            $msg_clienteGeneral . '<span class="style-cliente">' . $nombre . '</span>' .
            $msg_clienteAntiguo2 . '<span class="style-empresa">' . $nombreEmpresa. '</span>'; ?> 
        </h1>
    <?php endif;?>

    <div class="caja-mensaje-autom">
        <h2 class="encabezado-secundario"> 
            <?php echo 
            $msg_autom_encabezado . '<span class="style-cliente">'. $nombre . "</span>"; ?>
        </h2>
        <p class="mensaje-autom-cuerpo">
            <?php echo 
                $msg_autom_cuerpo1 . '<span class="style-empresa">' . $nombreEmpresa . '</span>' . 
                $msg_autom_cuerpo2 . '<span class="style-ticket">' . $ticket . '</span>' . 
                $msg_autom_cuerpo3 . '<span class="style-depto">' . $nombre_depto . '</span>' . 
                $msg_autom_cuerpo4 . '<span class="style-empleado">' . $nombreEmpleado . '</span>'; ?>
        </p>
        <p class="pie" >Gracias por contactarnos. Hasta pronto.</p>  
    </div>

    <section>
        <div class="contenedor contenido-tabla">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>No ticket</th>
                        <th>Fecha Creacion</th>
                        <th>Nombre Depto</th>
                        <th>Asesor Asignado</th>
                    </tr>
                </thead>

                <tbody>

<!-- 6. CONSULTANDO ASIGNACIONES DE SERVICIO DEL CLIENTE Y MOSTRANDOLAS EN PANTALLA -->
                    <?php foreach($solicitudes as $solicitud) {
                        echo "<tr>";
                            echo "<td>" . $solicitud['ticket'];
                            echo "<td>" . $solicitud['fecha creacion'];
                            echo "<td>" . $solicitud['nombre depto'];
                            echo "<td>" . $solicitud['nombreEmpleado'];
                        echo "<tr>";
                    }
                    ?>

                </tbody>
            </table>
        </div>        
    </section>
</body>
</html>

<?php
    //REQUIRIENDO EL CODIGO html DEL footer---------------
    require '../includes/footer.php';
?>