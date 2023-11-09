<?php

class Producto
{
    public $id;
    public $nombre;
    public $sector;
    public $precio;

    public function __construct($nombre, $sector, $precio, $id = null)
    {
        $this->nombre = $nombre;
        $this->sector = $sector;
        $this->precio = $precio;
        if($id != null){
            $this->id = $id;
        }
    }
    public function InsertarProducto()
	{
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("INSERT into productos (nombre, sector, precio)values('$this->nombre','$this->sector', '$this->precio')");
		$consulta->execute();
		return $objetoAccesoDato->RetornarUltimoIdInsertado();
	}
    public static function TraerTodoLosProductos()
	{
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select id_producto as id, nombre as nombre, sector as sector, precio as precio from productos");
        $consulta->execute();
        $arrayObtenido = array();
        $productos = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $producto = new Producto($i->nombre, $i->sector, $i->precio, $i->id );
            $productos[] = $producto;
        }
        return $productos;
	}
    public static function TraerUnProducto_Id($id) 
	{
        $producto = null;
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select * from productos where id_producto = ?");
        $consulta->bindValue(1, $id, PDO::PARAM_INT);
        $consulta->execute();
        $productoBuscado= $consulta->fetchObject();
        if($productoBuscado != null){
            $producto = new Producto($productoBuscado->nombre, $productoBuscado->sector, $productoBuscado->precio, $productoBuscado->id_producto,);
        }
        return $producto;
	}
    public static function TraerPrecio_Nombre($nombre) 
	{
        $precio = null;
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select precio from productos where nombre = ?");
        $consulta->bindValue(1, $nombre, PDO::PARAM_STR);
        $consulta->execute();
        $precio= $consulta->fetchObject();
        return $precio;
	}
    public static function TraerUnProducto_Nombre($nombre_producto) 
	{
        $producto = null;
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select * from productos where nombre = ?");
        $consulta->bindValue(1, $nombre_producto, PDO::PARAM_STR);
        $consulta->execute();
        $productoBuscado= $consulta->fetchObject();
        if($productoBuscado != null){
            $producto = new Producto($productoBuscado->nombre, $productoBuscado->sector, $productoBuscado->precio, $productoBuscado->id_producto,);
        }
        return $producto;
	}
    public static function MapearParaMostrar($array){
        if(count($array) > 0){
            foreach($array as $i){
                switch($i->sector){
                    case 1:
                        $i->sector = "Barra de tragos";
                    break;
                    case 2:
                        $i->sector = "Barra de choperas";
                    break;
                    case 3:
                        $i->sector = "Cocina";
                    break;
                    case 4:
                        $i->sector = "Candy bar";
                    break;
                }
            }
        }
        return $array;
    }
}
?>