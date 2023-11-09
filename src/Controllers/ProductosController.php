<?php
namespace App\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Producto;
use Autenticador;

require '../src/Clases/Producto.php';
require_once '../src/Clases/Autenticador.php';

class ProductosController
{
    public static function GET_TraerTodos(Request $request, Response $response, array $args){
        $productos = Producto::TraerTodoLosProductos();
        $productosMapp = Producto::MapearParaMostrar($productos);
        $listado = json_encode(array("Listado_de_productos"=>$productosMapp));
        $response->getBody()->write($listado);
        return $response;
    }
    public static function POST_InsertarProducto(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(!isset($param['token'])){
            $retorno = json_encode(array("mensaje" => "Token necesario"));
        }
        else{
            $token = $param['token'];
            $respuesta = Autenticador::ValidarToken($token, "Admin");
            if($respuesta == "Validado")
            {
                $parametros = $request->getParsedBody();
                $nombre = $parametros['nombre'];
                $sector = $parametros['sector'];
                $precio = $parametros['precio'];

                $producto = new Producto($nombre, $sector, $precio);
                $ok = $producto->InsertarProducto();
                echo "producto: ", $ok;
                if($ok != null){
                    $retorno = json_encode(array("mensaje" => "Producto creado con exito"));
                }
                else{
                    $retorno = json_encode(array("mensaje" => "No se pudo crear"));
                }        
            }       
            else{
                $retorno = json_encode(array("mensaje" => $respuesta));
            }
        }
        $response->getBody()->write($retorno);
        return $response;
    }
}