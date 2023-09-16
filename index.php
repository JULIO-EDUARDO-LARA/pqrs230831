<?php
    //0. REQUIRIENDO ARCHIVO DE CONEXION 
    require 'config/database.php';
    //1. BASE DE DATOS
    $db = conectarDB();

    //2. CONSULTANDO EL ID DE LOS DEPARTAMENTOS
    $consulta_tabla_depto = "SELECT * FROM departamento";
    $resultado_tabla_depto = mysqli_query($db, $consulta_tabla_depto);

    //3. DECLARANDO ARREGLO VACIO DE ERRORES
    $errores = [];

    //4. INICIALIZANDO VARIABLES
    $clienteExiste = "1";

    $email = '';
    $nombre = '';
    $iddepto = 0;
    $nombreEmpleado = '';
    $mensaje = '';
    $fecha = '';
    // 5. VALIDACION: SI EL REQUEST METHOD ES DE TIPO POST----------------------------------

    if( $_SERVER['REQUEST_METHOD'] ==='POST' ){

        //5.1 EXTRAER VARIABLES DE $_POST TABLA `clientes`
        $email = $_POST['email'];
        $nombre = $_POST['nombre'];

        //5.2 EXTRAER VARIABLES DE $_POST TABLA `clientes`
        $iddepto = (int)$_POST['iddepto'];
        $mensaje = $_POST['mensaje'];
        $fecha = date("Y-m-d h:i:s");

        //5.3 VALIDACION EN ARREGLO $errores SI: VARIABLES CAPTURADAS ESTAN VACIAS = true
        if(!$email) {
            $errores [] = "Ingresa un correo Electrónico";}
        if(!$nombre) {
            $errores [] = "Ingresa un tu nombre";}
        if($iddepto === 0) {
            $errores [] = "selecciona un departamento";}
        if(!$mensaje) {
            $errores [] = "debe redactar un mensaje";}
        
        // 5.4 VERIFICANDO QUE ARREGLO $errores ESTA VACIO = true 
        if(empty($errores)){

            //5.4.1 CONSULTAR NOMBRE DEL DEPARTAMENTO EN TABLA `departamento`
            $consult_nombre_depto = "SELECT `nombre depto` FROM departamento WHERE iddepto=$iddepto";
            $consult_nombre_depto = mysqli_query($db, $consult_nombre_depto);
            $array_nombre_depto = mysqli_fetch_assoc($consult_nombre_depto);
            $nombre_depto = $array_nombre_depto['nombre depto'];

            //5.4.2 CONSULTANDO EL CLIENTE
            $consultarCliente = " SELECT email FROM clientes WHERE email = '$email' ";
            $resultado_existeCliente = mysqli_query($db, $consultarCliente);
  
            //5.4.2.1 ACCEDIENDO AL ATRIBUTO num_rows del objeto (mysqli_result)
            $ExisteCliente = $resultado_existeCliente->num_rows;

            //5.4.2.2 VALIDANDO SI: EL CLIENTE NO EXISTE = true
            if($ExisteCliente === 0){
                //5.4.2.2.1 INGRESAR EL CLIENTE A LA TABLA `clientes`
                $queryClientes = "INSERT INTO clientes (email, nombre) VALUES ('$email','$nombre')";
                $resultadoClientes = mysqli_query($db, $queryClientes);
                //5.4.2.2.2 VALIDANDO SI: CLIENTE INSERTADO = true
                if($resultadoClientes) {
                    //5.4.2.2.2.1 VARIABLE DE CONTROL PARA MENSAJE DE BIENVENIDA
                    $clienteExiste = "0";
                    echo "Ahora usted ingreso a nuestra base de datos";
                }
            }

            //5.4.2.3 CONSULTANDO EL ID DEL LOS EMPLEADOS----------------
            $id = [];
            $consulta_id_asesores = "SELECT `id empleado` FROM empleados WHERE iddepto = $iddepto";
            $result_id_asesores = mysqli_query($db, $consulta_id_asesores);
                //5.4.2.3.1 GUARDANDO EN UN ARRAY EL ID DEL LOS EMPLEADOS----------------
                while($row = mysqli_fetch_assoc($result_id_asesores)):
                    $id [] = $row['id empleado'];
                endwhile;
                //5.4.2.3.2 ESCOGER ALEATORIAMENTE EL ID DE UN EMPLEADO----------------
                $random_id_asesor = $id[array_rand($id)];

            //5.4.2.4 CONSULTANDO EL NOMBRE DEL EMPLEADO----------------
            $consult_name_asesor = "SELECT nombreEmpleado FROM empleados WHERE `id empleado`=$random_id_asesor";
            $result_consult_name_asesor = mysqli_query($db, $consult_name_asesor);
            $array_nombreEmpleado = mysqli_fetch_assoc($result_consult_name_asesor);
            $nombreEmpleado = $array_nombreEmpleado['nombreEmpleado'];

            //5.4.2.5 INSERTANDO ASIGNACION DEL SERVICIO en tabla 'asignaciones'----------------
            $queryAsignaciones = "INSERT INTO asignaciones (email, iddepto, nombreEmpleado, mensaje, fecha) VALUES ('$email',$iddepto ,'$nombreEmpleado', 'mensaje', '$fecha')";
            $resultadoAsignaciones = mysqli_query($db, $queryAsignaciones);

            //5.4.2.6 SI: LA INSERCION EN TABLA asignacion = TRUE
            if($resultadoAsignaciones){
                //5.4.2.6.1 CAPTURAR NUMERO DE TICKET DE LA TABLA asignaciones---------------
                $consulta_asignacion = "SELECT MAX(`id asignacion`) AS `ultimo ticket` FROM asignaciones WHERE email='$email'";
                $consulta_nombre = "SELECT `nombre` FROM clientes WHERE email='$email'";

                $resultado_consulta_asignacion = mysqli_query($db, $consulta_asignacion);
                $resultado_consulta_nombre = mysqli_query($db, $consulta_nombre);

                //5.4.2.6.2 CAPTURANDO Y GUARDANDO EN ULTIMO NUMERO DE TICKET DE LA TABLA asignaciones---------------
                while($rows = mysqli_fetch_assoc($resultado_consulta_asignacion)):
                    $ticket = $rows['ultimo ticket'];
                endwhile;

                //5.4.2.6.3 CAPTURANDO Y GUARDANDO EL NOMBRE DEL CLIENTE DE LA TABLA clientes---------------
                while($rows = mysqli_fetch_assoc($resultado_consulta_nombre)):
                    $nombre = $rows['nombre'];
                endwhile;

                //5.4.2.6.4 REDIRECCIONANDO A PAGINA thankyou.php---------------
                header('Location: http://localhost/pqrs230831/internal_pages/thankyou.php?nombre='. $nombre . '&nombreEmpleado=' . $nombreEmpleado . '&ticket=' . $ticket . '&nombre_depto=' . $nombre_depto . '&clienteExiste=' . $clienteExiste. '&email=' .$email);
            }
        }
    }
    //REQUIRIENDO EL CODIGO html DEL header---------------
    require 'includes/header.php';
?>

    <h1 class="encabezado-principal">FORMULARIO DE CONTACTO</h1>
    <div class="contenedor contenido-form">
        <form class="formulario" method="POST" action="http://localhost/pqrs230831/index.php">

            <?php foreach( $errores as $error ): ?>
            <div class="alerta-error">
                <?php echo $error; ?>
            </div>
            <?php endforeach; ?>

            <fieldset>
                <legend>informacion Personal</legend>

                <label for="nombre">Nombre</label>
                <input type="text" placeholder="tu Nombre" id="nombre" name="nombre" value="<?php echo $nombre; ?>">

                <label for="nombre">Correo Electrónico</label>
                <input type="email" placeholder="tu Email" id="email" name="email"  value="<?php echo $email; ?>">
            </fieldset>
            
            <fieldset>
                <legend>Informacion remitida</legend>

                <label for="iddepto">Departamento que recibe </label>
                <select id="iddepto" name="iddepto"  value="">

                
                    <option value="0">-- Seleccione --</option>
                    <?php while($row = mysqli_fetch_assoc($resultado_tabla_depto)): ?>
                        <option <?php echo $iddepto == $row['iddepto'] ? 'selected' : ''; ?> 
                        value="<?php echo $row['iddepto'];?>">
                        <?php echo $row['nombre depto']; ?>
                        </option>
                    <?php endwhile; ?>
                    <!-- <option value="10" >atención al cliente</option>
                    <option value="20" >facturación</option>
                    <option value="30" >soporte técnico</option> -->
                </select>

                <label for="mensaje">Mensaje</label>
                <textarea id="mensaje" name="mensaje"><?php echo $mensaje; ?></textarea>
            </fieldset>

            <input class="boton" type="submit" value="Enviar">
        </form>        
    </div>

<?php
    //REQUIRIENDO EL CODIGO html DEL footer---------------
    require 'includes/footer.php';
?>
 