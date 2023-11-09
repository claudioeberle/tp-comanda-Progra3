<?php
require_once '../src/Clases/Producto.php';
class Pedido
{
    public $id;
    public $numero_pedido;
    public $items;

    public function __construct($items = null, $numero_pedido = null, $id = null)
    {
        $this->items = array();
        if($items != null){
            $this->items = $items; 
        }
        if($numero_pedido == null){
            $this->numero_pedido = rand(1, 99999);
        }
        else{
            $this->numero_pedido = $numero_pedido;
        }
        if($id != null){
            $this->id = $id;
        }
    }
    public function Alta_pedido()
	{
        $itemsJson = json_encode($this->items);
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("insert into pedidos (numero_pedido, items)values('$this->numero_pedido','$itemsJson')");
		$consulta->execute();
		return $objetoAccesoDato->RetornarUltimoIdInsertado();
	}
    public static function TraerTodoLosPedidos()
	{
        $pedido = null;
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select id as id, numero_pedido as numero_pedido, items as items from pedidos");
        $consulta->execute();
        $arrayObtenido = array();
        $pedidos = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $itemsJson = json_decode($i->items);
            $pedido = new Pedido($itemsJson, $i->numero_pedido, $i->id );
            $pedidos[] = $pedido;
        }
        return $pedidos;
	}
    public static function TraerUnPedido_Id($id) 
	{
        $pedido = null;
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select * from pedidos where id = ?");
        $consulta->bindValue(1, $id, PDO::PARAM_INT);
        $consulta->execute();
        $pedidoBuscado= $consulta->fetchObject();
        if($pedidoBuscado != null){
            $itemsJson = json_decode($pedidoBuscado->items);
            $pedido = new Pedido($itemsJson, $pedidoBuscado->numero_pedido, $pedidoBuscado->id,);
        }
        return $pedido;
	}
    public static function TraerUnPedido_Numero_pedido($numero_pedido) 
	{
        $pedido = null;
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select * from pedidos where numero_pedido = ?");
        $consulta->bindValue(1, $numero_pedido, PDO::PARAM_INT);
        $consulta->execute();
        $pedidoBuscado= $consulta->fetchObject();
        if($pedidoBuscado != null){
            $itemsJson = json_decode($pedidoBuscado->items);
            $pedido = new Pedido($itemsJson, $pedidoBuscado->numero_pedido, $pedidoBuscado->id,);
        }
        return $pedido;
	}
    public function Cargar_item_nuevo($id_producto)
    {
        $producto = Producto::TraerUnProducto_Id($id_producto);
        $producto_pedido = array(
            "nombre"=>$producto->nombre,
            "estado"=>0,
            "tiempo"=>0,
        );
        array_push($this->items,$producto_pedido);
    }
    public function Cambiar_estado_item($id_producto, $estado)
    {
        $sector = null;
        $producto = Producto::TraerUnProducto_Id($id_producto);
        foreach($this->items as $i){
            if($i->nombre == $producto->nombre){
                $i->estado = $estado;
                $sector = $producto->sector;
            }
        }
        return $sector;
    }
    
    public function Agregar_tiempo_item($id_producto, $tiempo_minutos)
    {
        $ok = false;
        $producto = Producto::TraerUnProducto_Id($id_producto);
        foreach($this->items as $i){
            if($i->nombre == $producto->nombre){
                $i->tiempo = $tiempo_minutos;
                $ok = true;
            }
        }
        return $ok;
    }
    public function Calcular_tiempo_total_pedido(){
        $tiempo_demora = 0;
        foreach($this->items as $i){
            if($i->tiempo > $tiempo_demora){
                $tiempo_demora = $i->tiempo;
            }       
        }
        return $tiempo_demora;
    }
    
    public function Actualizar_items_BD()
    {
        $itemsJson = json_encode($this->items);
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("update pedidos set items = ? where id = ?");
        $consulta->bindValue(1, $itemsJson, PDO::PARAM_STR);
        $consulta->bindValue(2, $this->id, PDO::PARAM_INT);
        return$consulta->execute();
    }
    public static function MapearParaMostrar($array){
        if(count($array) > 0){
            foreach($array as $i){
                foreach($i->items as $p){
                    switch($p->estado){
                        case 0:
                            $p->estado = "Pendiente";
                        break;
                        case 1:
                            $p->estado = "En preparacion";
                        break;
                        case 2:
                            $p->estado = "Listo para servir";
                        break;
                    }
                }
                
            }
        }
        return $array;
    }
    public static function FiltrarSegunSector($array, $sector, $estado = null){
        $arrayFiltrado = array();
        $itemsFillter = array();
        if(count($array) > 0){
            foreach($array as $i){
                foreach($i->items as $p){
                    $producto = Producto::TraerUnProducto_Nombre($p->nombre);
                    if($producto->sector == $sector){
                        if($estado != null){
                            if($p->estado == $estado){
                                array_push($itemsFillter, $p);
                            }
                            
                        }
                        else{
                            array_push($itemsFillter, $p);
                        }
                    }
                }
                $i->items = $itemsFillter;
                $itemsFillter = array();
                if(count($i->items) > 0){
                    array_push($arrayFiltrado, $i);
                }
            }
        }
        return $arrayFiltrado;
    }
    public static function FiltrarSegun_estado($array, $estado){
        $arrayFiltrado = array();
        $itemsFillter = array();
        if(count($array) > 0){
            foreach($array as $i){
                foreach($i->items as $p){
                    if($p->estado == $estado){
                        
                        array_push($itemsFillter, $p);
                    }
                }
                $i->items = $itemsFillter;
                $itemsFillter = array();
                if(count($i->items) > 0){
                    array_push($arrayFiltrado, $i);
                }
            }
        }
        return $arrayFiltrado;
    }
    public static function Comprobar_estado_pedido_listo($array){
        $arrayFiltrado = array();
        $itemsFillter = array();
        if(count($array) > 0){
            foreach($array as $i){
                $items_cantidad = count($i->items);
                foreach($i->items as $p){
                    if($p->estado == 2){
                        
                        array_push($itemsFillter, $p);
                    }
                }
                if($items_cantidad == count($itemsFillter)){
                    $i->items = $itemsFillter;
                    array_push($arrayFiltrado, $i);
                }
                $itemsFillter = array();
            }
        }
        return $arrayFiltrado;
    }
}


