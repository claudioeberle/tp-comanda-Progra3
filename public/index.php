<?php

use App\Controllers\MesasController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use App\Controllers\UsuariosController;
use App\Controllers\ProductosController;
use App\Controllers\PedidosController;
use App\Controllers\ComandasController;

require __DIR__ . '/../vendor/autoload.php';
require '../src/AccesoDatos.php';
// Instantiate app
$app = AppFactory::create();

// Add Error Handling Middleware
$app->addErrorMiddleware(true, false, false);

$app->group('/usuarios', function (RouteCollectorProxy $group) {
  if(!isset($_GET['accion'])){
    $group->post('[/]', UsuariosController::class . ':ErrorDatos');
    $group->get('[/]', UsuariosController::class . ':ErrorDatos');
  }
  else{
    switch($_GET['accion']){
      case "login":
        $group->post('[/]', UsuariosController::class . ':POST_Login');
      break;
      case "listar":
        $group->get('[/]', UsuariosController::class . ':GET_TraerTodos');
      break;
      case "guardarEnCSV":
        $group->get('[/]', UsuariosController::class . ':GET_GaurdarEnCSV');
      break;
      case "leerDeCSV":
        $group->get('[/]', UsuariosController::class . ':GET_CargarUsuariosCSV');
      break;
      case "insertar":
        $group->post('[/]', UsuariosController::class . ':POST_InsertarUsuario');
      break;
    }
  }
  
  });
  $app->group('/productos', function (RouteCollectorProxy $group) {
    if(!isset($_GET['accion'])){
      $group->post('[/]', UsuariosController::class . ':ErrorDatos');
      $group->get('[/]', UsuariosController::class . ':ErrorDatos');
    }
    else{
      switch($_GET['accion']){
        case "listar":
          $group->get('[/]', ProductosController::class . ':GET_TraerTodos');
        break;
        case "insertar":
          $group->post('[/]', ProductosController::class . ':POST_InsertarProducto');
        break;
      }
    }
  });

  $app->group('/mesas', function (RouteCollectorProxy $group) {
    if(!isset($_GET['accion'])){
      $group->post('[/]', UsuariosController::class . ':ErrorDatos');
      $group->get('[/]', UsuariosController::class . ':ErrorDatos');
    }
    else{
      switch($_GET['accion']){
        case "listar":
          $group->get('[/]', MesasController::class . ':GET_TraerTodos');
        break;
        case "alta":
          $group->post('[/]', MesasController::class . ':POST_Alta_de_mesa');
        break;
        case "cambiarEstado":
          $group->post('[/]', MesasController::class . ':POST_cambiar_estado_de_mesa');
        break;
      }
    }
  });
  $app->group('/pedidos', function (RouteCollectorProxy $group) {
    if(!isset($_GET['accion'])){
      $group->get('[/]', UsuariosController::class . ':ErrorDatos');
      $group->post('[/]', UsuariosController::class . ':ErrorDatos');
    }
    else{
      switch($_GET['accion']){
        case "listar":
          $group->get('[/]', PedidosController::class . ':GET_TraerTodos');
        break;
        case "listarPendientes":
          $group->get('[/]', PedidosController::class . ':GET_Listar_pedidos_segun_estado');
        break;
        case "consultarTiempo":
          $group->get('[/]', PedidosController::class . ':GET_ConsultarTiempo');
        break;
        case "alta":
          $group->post('[/]', PedidosController::class . ':POST_AltaPedido');
        break;
        case "cambiarEstado":
          $group->post('[/]', PedidosController::class . ':POST_cambiar_estado_pedido');
        break;
        
      }
    }
  });
  $app->group('/comandas', function (RouteCollectorProxy $group) {
    if(!isset($_GET['accion'])){
      $group->get('[/]', UsuariosController::class . ':ErrorDatos');
      $group->post('[/]', UsuariosController::class . ':ErrorDatos');
    }
    else{
      switch($_GET['accion']){
        case "listar":
          $group->get('[/]', ComandasController::class . ':GET_TraerTodos');
        break;
        case "listarListos":
          $group->get('[/]', ComandasController::class . ':GET_Listar_pedidos_listos_cambiar_esatdo_mesa');
        break;
        case "listarMejorPuntuacion":
          $group->get('[/]', ComandasController::class . ':GET_ListarMejoresPuntuaciones');
        break;
        case "mesaMasUsada":
          $group->get('[/]', ComandasController::class . ':GET_MesaMasUsada');
        break;
        case "alta":
          $group->post('[/]', ComandasController::class . ':POST_AltaComanda');
        break;
        case "consultarTiempo":
          $group->post('[/]', ComandasController::class . ':POST_Ver_tiempo_restante');
        break;
        case "guardarImagen":
          $group->post('[/]', ComandasController::class . ':POST_Guardar_imagen');
        break;
        case "cobrar":
          $group->post('[/]', ComandasController::class . ':POST_CobrarComanda');
        break;
        case "encuesta":
          $group->post('[/]', ComandasController::class . ':POST_CienteEncuesta');
        break;
      }
    }
  });

// Run application
$app->run();