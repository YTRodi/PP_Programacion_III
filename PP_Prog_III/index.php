<?php

include_once __DIR__.'/clases/AuthJWT.class.php';
include_once __DIR__.'/clases/FileHandler.class.php';
include_once __DIR__.'/clases/Usuario.class.php';
include_once __DIR__.'/clases/Precio.class.php';
include_once __DIR__.'/clases/Ingreso.class.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['PATH_INFO'] ?? 0;
$jwt = $_SERVER['HTTP_TOKEN'] ?? '';

try {

    $jwtDecodificado = AuthJWT::ValidarToken( $jwt );

    // print_r(AuthJWT::GetDatos( $_SERVER['HTTP_TOKEN'] ));

    // print_r($jwtDecodificado);

} catch (\Throwable $e) {

    echo $e;
    
    //var_dump($e->getTrace());

}

switch ($path) {

    case '/registro':

        switch ($method) {

            case 'POST':

                try {
                    
                    $listaUsuariosJSON = Usuario::ReadUsuarioJSON();
                    
                    $email = $_POST['email'] ?? '';
                    $tipo = $_POST['tipo'] ?? '';
                    $password = $_POST['password'] ?? '';

                    if( $tipo === 'admin' || $tipo === 'user' ) {

                        $nuevoUsuario = new Usuario($email, $tipo,$password);

                        $emailRegistrado = $nuevoUsuario->EmailUnico( $listaUsuariosJSON );

                        if( !$emailRegistrado ) {

                            array_push($listaUsuariosJSON,$nuevoUsuario);
                            //var_dump($listaUsuariosJSON);
                            Usuario::SaveUsuarioJSON($listaUsuariosJSON);

                        }else {

                            throw new Exception('</br>El Email ya está registrado!</br>');

                        }
                 

                    }else {

                        throw new Exception( '</br>Tipo incorrecto.(Sólo puede ser admin o user)</br>' );

                    }

                } catch (\Throwable $e) {

                    echo 'Mensaje de error: ' . $e->getMessage() . '</br>';
                    var_dump($e->getTrace());

                }

                break;
        }

        break;

    case '/login':

        switch ($method) {

            case 'POST':

                try {

                    $listaUsuariosJSON = Usuario::ReadUsuarioJSON();

                    $email = $_POST['email'] ?? '';
                    $password = $_POST['password'] ?? '';

                    foreach ($listaUsuariosJSON as $key) {
                        if($key->_tipoUsuario === 'admin') {
                            
                            $nuevoUsuario = new Usuario($email, $key->_tipoUsuario,$password);
                            
                        }else if($key->_tipoUsuario === 'user') {

                            $nuevoUsuario = new Usuario($email, $key->_tipoUsuario,$password);

                        }
                    }

                    $estaLaPatente = $nuevoUsuario->verificarUsuario($listaUsuariosJSON);

                    if($estaLaPatente){

                        $payload = ['email' => $nuevoUsuario->_email,
                                    'tipo' =>$nuevoUsuario->_tipoUsuario ];

                        $token = AuthJWT::Login( $payload );

                        print_r($token);

                        //var_dump(AuthJWT::ValidarToken($token));
                        
                        echo '</br>Login con éxito!</br>';

                    }else{

                        echo '</br>LOGIN SIN ÉXITO :(</br>';

                    }

                } catch (\Throwable $e) {

                    echo 'Mensaje de error: ' . $e->getMessage() . '</br>';
                    var_dump($e->getTrace());

                }

                break;
        }

        break;

    case '/precio':

        if($jwtDecodificado->tipo === 'admin'){ //Verifico JWT por el header

            switch ($method) {

                case 'POST':

                    try {

                        $listaPreciosJSON = Precio::ReadPrecioJSON();

                        $pHora = $_POST['precio_hora'] ?? '';
                        $pEstadia = $_POST['precio_estadia'] ?? '';
                        $pMensual = $_POST['precio_mensual'] ?? '';
    
                        $nuevoPrecio = new Precio( $pHora, $pEstadia, $pMensual );
                        array_push($listaPreciosJSON,$nuevoPrecio);
                        Precio::SavePrecioJSON( $listaPreciosJSON );
    
                    } catch (\Throwable $e) {

                        echo 'Mensaje de error: ' . $e->getMessage() . '</br>';
                        var_dump($e->getTrace());

                    }
                    break;

            }

        }else{

            echo '</br>El usuario no es admin.</br>';

        }

        break;

    case '/ingreso':
        //!! BORRAR!!!
        //!! BORRAR!!!
        //!! BORRAR!!!
        //!! BORRAR!!!
        //!! BORRAR!!!

        //DECODIFICO EL TOKEN QUE ESTÁ INGRESADO ACTUALMENTE EN POSTMAN!
        $jwtDecodificado->tipo = 'user';

        //!! BORRAR!!!
        //!! BORRAR!!!
        //!! BORRAR!!!
        //!! BORRAR!!!
        //!! BORRAR!!!

        if($jwtDecodificado->tipo === 'user'){ //Verifico JWT por el header

            switch ($method) {

                case 'POST':

                    try {
                        //Email y tipo están en el token.
                        $listaIngresoJSON = Ingreso::ReadIngresoJSON();

                        $patente = $_POST['patente'] ?? '';
                        $fechaIngreso = date('d - H:m:s');

                        if( $_POST['tipo'] === 'hora' || $_POST['tipo'] === 'estadia' || $_POST['tipo'] === 'mensual' ) {

                            $tipo = $_POST['tipo'] ?? '';

                        }else {

                            throw new Exception( '</br>Tipo enviado por POST erróneo. (Sólo se aceptan: hora, estadia o mensual.</br>' );

                        }
                        $emailUsuario = $jwtDecodificado->email;

                        $nuevoIngreso = new Ingreso( $patente, $fechaIngreso, $tipo, $emailUsuario );

                        array_push( $listaIngresoJSON, $nuevoIngreso);
                        Ingreso::SaveIngresoJSON( $listaIngresoJSON );
    
                    } catch (\Throwable $e) {

                        echo 'Mensaje de error: ' . $e->getMessage() . '</br>';
                        var_dump($e->getTrace());

                    }
                    break;
    
                case 'GET':
                    
                    //PUNTO 6
                    $listaIngresoJSON = Ingreso::ReadIngresoJSON();
                    
                    echo 'AUTOS EN EL ESTACIONAMIENTO: </br></br>';
                    foreach ($listaIngresoJSON as $key ) {
                        echo $key . '</br>' ;
                    }

                    echo '</br>';
                    echo '</br>';
                    
                    //------------------------------------------------------------------------
                    //------------------------------------------------------------------------
                    //PUNTO 7
                    $patente = $_GET['patente'] ?? '';
                    $estaLaPatente = false;
                    $auto;
            
                    foreach ($listaIngresoJSON as $key ) {
        
                        if($key->_patente === $patente ){
                            $estaLaPatente = true;
                            $auto = $key;
                        }
                    }

                    if($estaLaPatente) {

                        echo $auto;

                    }else {

                        echo '</br>LA PATENTE NO SE ENCUENTRA EN EL ESTACIONAMIENTO.</br>';

                    }

                    break;

            }

        }else{

            echo '</br>Usted no es del tipo user.</br>';

        }

        break;

    break;
}

