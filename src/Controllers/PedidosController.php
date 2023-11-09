<?php
namespace App\Controllers;

use Autenticador;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Pedido;
use Usuario;

require '../src/Clases/Pedido.php';
require_once '../src/Clases/Usuario.php';
require_once '../src/Clases/Autenticador.php';

class PedidosController
{
    public static function GET_TraerTodos(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(!isset($param['token'])){
            $respuesta = json_encode(array("mensaje" => "Token necesario"));
        }
        else{
            $token = $param['token'];
            $respuesta = Autenticador::ValidarToken($token, "Admin");
            if($respuesta == "Validado"){
                $pedidos = Pedido::TraerTodoLosPedidos();
                $pedidosMapp = Pedido::MapearParaMostrar($pedidos);
                $respuesta = json_encode(array("Listado_de_pedidos"=>$pedidosMapp));
            }
            else{
                $sector = Autenticador::TraerSectorDesdeToken($token);
                $pedidos = Pedido::TraerTodoLosPedidos();
                $pedidosFiltrados = Pedido::FiltrarSegunSector($pedidos, $sector);
                $pedidosMapp = Pedido::MapearParaMostrar($pedidosFiltrados);
                $respuesta = json_encode(array("Listado_de_pedidos"=>$pedidosMapp));
            }
        }
        $response->getBody()->write($respuesta);
        return $response;
    }
    public static function POST_AltaPedido(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(!isset($param['token'])){
            $retorno = json_encode(array("mensaje" => "Token necesario"));
        }
        else{
            $token = $param['token'];
            $respuesta = Autenticador::ValidarToken($token, "Empleado" ,0);
            if($respuesta == "Validado")
            {
                $pedido = new Pedido();
                $parametros = $request->getParsedBody();
                $cadena_items = $parametros['items'];
                $elementos = explode(",", $cadena_items);
                foreach($elementos as $i){
                    echo $i;
                    $pedido->Cargar_item_nuevo($i);
                }
                $id_insertado = $pedido->Alta_pedido();
                if($id_insertado != null){
                    $retorno = json_encode(array("mensaje" => "Pedido creado con exito"));
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
    public static function POST_cambiar_estado_pedido(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(!isset($param['token'])){
            $retorno = json_encode(array("mensaje" => "Token necesario"));
        }
        else{
            $parametros = $request->getParsedBody();
            if(!isset($parametros['numero_pedido'], $parametros['id_producto'], $parametros['estado'])){
                $retorno = json_encode(array("mensaje" => "Error en la carga de datos"));
            }
            else{
                $numero_pedido = $parametros['numero_pedido'];
                $id_producto = $parametros['id_producto'];
                $estado = $parametros['estado'];
                $tiempoOK = 1;
                $pedido = Pedido::TraerUnPedido_Numero_pedido($numero_pedido);
                if($pedido == null){
                    $retorno = json_encode(array("mensaje" => "El numero de pedido es invalido"));
                }
                else{
                    $sector = $pedido->Cambiar_estado_item($id_producto, $estado);
                    if($estado == 1){
                        $tiempoOK = 0;
                        if(!isset($parametros['tiempo'])){
                            $retorno = json_encode(array("mensaje" => "ingrese el tiempo de elaboracion"));
                        }
                        else{
                            $tiempo = $parametros['tiempo'];
                            if(!($pedido->Agregar_tiempo_item($id_producto, $tiempo))){
                                $retorno = json_encode(array("mensaje" => "No se pudo realizar"));
                            }
                            else{
                                $tiempoOK = 1;
                            }
                        }
                    }
                    else{
                        if($estado == 2){
                            $pedido->Agregar_tiempo_item($id_producto, 0);
                        }
                    }
                    if($sector == null){
                        $retorno = json_encode(array("mensaje" => "No se pudo realizar"));
                    }
                    else{
                        $token = $param['token'];
                        $respuesta = Autenticador::ValidarToken($token, "Empleado" ,$sector);
                        if($respuesta == "Validado"){
                            if($tiempoOK == 1){
                                $pedido->Actualizar_items_BD();
                                $retorno = json_encode(array("mensaje" => "Estado actualizado con exito"));
                            }
                            else{
                                $retorno = json_encode(array("mensaje" => "ingrese el tiempo de elaboracion"));
                            }
                        }
                        else{
                            $retorno = json_encode(array("mensaje" => $respuesta));
                        }
                    }
                }
            }
        }
        $response->getBody()->write($retorno);
        return $response;
    }
    public static function GET_ConsultarTiempo(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(!isset($param['numero_pedido'])){
            $retorno = json_encode(array("mensaje" => "ingrese numeo de pedido"));
        }
        else{
            $numero_pedido = $param['numero_pedido'];
            $pedido = Pedido::TraerUnPedido_Numero_pedido($numero_pedido);
            $tiempo = $pedido->Calcular_tiempo_total_pedido();
            $retorno = json_encode(array("La demora es" => $tiempo." minutos"));
        }
        $response->getBody()->write($retorno);
        return $response;
    }
    public static function GET_Listar_pedidos_segun_estado(Request $request, Response $response, array $args){
        $param = $request->getQueryParams();
        if(!isset($param['token'])){
            $retorno = json_encode(array("mensaje" => "Token necesario"));
        }
        else{
            $token = $param['token'];
            if(isset($param['estado'])){
                $estado = $param['estado'];
                $respuesta = Autenticador::ValidarToken($token, "Admin");
                if($respuesta == "Validado"){
                    $pedidos = Pedido::TraerTodoLosPedidos();
                    $pedidosFiltrados = Pedido::FiltrarSegun_estado($pedidos,  $estado);
                    $pedidosMapp = Pedido::MapearParaMostrar($pedidosFiltrados);
                    $retorno = json_encode(array("Listado_de_pedidos"=>$pedidosMapp));
                }
                else{
                    if($respuesta = Autenticador::ValidarToken($token, "Empleado", 0) == "Validado"){
                        $pedidos = Pedido::TraerTodoLosPedidos();
                        $pedidosFiltrados = Pedido::FiltrarSegun_estado($pedidos,  $estado);
                        $pedidosMapp = Pedido::MapearParaMostrar($pedidosFiltrados);
                        $retorno = json_encode(array("Listado_de_pedidos"=>$pedidosMapp));
                    }
                    else{
                        $sector = Autenticador::TraerSectorDesdeToken($token);
                        $pedidos = Pedido::TraerTodoLosPedidos();
                        $pedidosFill = Pedido::FiltrarSegun_estado($pedidos,  $estado);
                        $pedidosFiltrados = Pedido::FiltrarSegunSector($pedidosFill, $sector, 0);
                        $pedidosMapp = Pedido::MapearParaMostrar($pedidosFiltrados);
                        $retorno = json_encode(array("Listado_de_pedidos"=>$pedidosMapp));
                    }
                    
                }
            }
            else{
                $retorno = json_encode(array("mensaje" => "Ingrese estado a consultar"));
            }
            
        }
        $response->getBody()->write($retorno);
        return $response;
    }
}

