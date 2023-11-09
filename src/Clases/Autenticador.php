<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once '../src/Clases/Usuario.php';

class Autenticador
{
    private static $claveSecreta = "miClaveSecreta123";
    private static $tipoEncriptacion = "HS256";

    public static function Definir_token($id, $email){
        $time = time();
        $payload = array(
         
            "iat" => $time, //Tiempo en que inicia el token
            "exp" => $time + (60*60*24), //Tiempo de expiracion del token (1 dia)
            "data" => [
                "id" => $id,
                "email" => $email,
            ]
        );
        $token = JWT::encode($payload, self::$claveSecreta, self::$tipoEncriptacion);
        return $token;
    }
    public static function ValidarToken($token, $tipo, $sector = null){
        $usuario = null;
        $resp = "No autorizado";
        try {
            $decodificado = JWT::decode(
                $token,
            new Key(self::$claveSecreta, self::$tipoEncriptacion)
            );
        $usuario = Usuario::TraerUnUsuarioId($decodificado->data->id);
        if($usuario != null && $usuario->tipo == $tipo){
            if($sector != null){
                if($usuario->sector == $sector){
                    $resp =  "Validado";
                }
            }
            else{
                $resp =  "Validado";
            }
        }
        } catch (Exception $e) {
            switch($e->getMessage()){
                case "Expired token":
                $resp = "Sesion expirada"; 
                break;
                case "Signature verification failed":
                    $resp = "Token invalido";
                    break;
            }
            die(json_encode(array("mensaje" => $resp)));
        }
        return $resp;
    }
    public static function TraerSectorDesdeToken($token){
        $usuario = null;
        $decodificado = JWT::decode(
            $token,
        new Key(self::$claveSecreta, self::$tipoEncriptacion)
        );
        $usuario = Usuario::TraerUnUsuarioId($decodificado->data->id);
        $resp = $usuario->sector;
        return $resp;
    }
}

?>