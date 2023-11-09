<?php
class Mesa
{
    public $id;
    public $estado;

    public function __construct($estado, $id = null)
    {
        $this->estado = $estado;
        if($id != null){
            $this->id = $id;
        }
    }
    public function InsertarMesa()
	{
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("INSERT into mesas (estado)values('$this->estado')");
		$consulta->execute();
		return $objetoAccesoDato->RetornarUltimoIdInsertado();
	}
    public static function TraerTodoLasMesas()
	{
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select id as id, estado as estado from mesas");
        $consulta->execute();
        $arrayObtenido = array();
        $mesas = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $mesa = new Mesa($i->estado, $i->id );
            $mesas[] = $mesa;
        }
        return $mesas;
	}
    public function CambiarEstadoMesa($estado){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("update mesas set estado = ? where id = ?");
        $consulta->bindValue(1, $estado, PDO::PARAM_INT);
        $consulta->bindValue(2, $this->id, PDO::PARAM_INT);
        return$consulta->execute();
    }
    public static function CambiarEstadoMesa_Id($estado, $id_mesa){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("update mesas set estado = ? where id = ?");
        $consulta->bindValue(1, $estado, PDO::PARAM_INT);
        $consulta->bindValue(2, $id_mesa, PDO::PARAM_INT);
        return$consulta->execute();
    }
    public static function MapearParaMostrar($array){
        if(count($array) > 0){
            foreach($array as $i){
                switch($i->estado){
                    case 1:
                        $i->estado = "Con cliente esperando pedido";
                    break;
                    case 2:
                        $i->estado = "Con cliente comiendo";
                    break;
                    case 3:
                        $i->estado = "Con cliente pagando";
                    break;
                    case 4:
                        $i->estado = "Cerrada";
                    break;
                }
            }
        }
        return $array;
    }
}
?>