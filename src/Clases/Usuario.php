<?php
class Usuario
{
    public $id;
    public $nombre;
    public $apellido;
    public $tipo;
    public $subTipo;
    public $sector;
    public $email;
    public $password;
    public $token;
    public $fechaRegistro;

    public function __construct($nombre, $apellido, $tipo, $email, $password = null, $subTipo = null, $sector = null, $fechaRegistro = null, $id = null, $token = null)
    {
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->tipo = $tipo;
        $this->email = $email;
        if($id != null){
            $this->id = $id;
        }
        if($subTipo != null){
            $this->subTipo = $subTipo;
        }
        if($sector != null){
            $this->sector = $sector;
        }
        if($fechaRegistro == null){
            $this->fechaRegistro =  date("Y-m-d");
        }
        else{
            $this->fechaRegistro = $fechaRegistro;
        }
        if($password != null){
            $this->password = $password;
        }
        else{
            $this->password = '12345';
        }
        if($token != null){
            $this->token = $token;
        }
    }
    public function InsertarUsuario()
	{
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("insert into usuarios (nombre,apellido,tipo, sub_tipo, sector, email, contraseña, token, fecha_registro)values('$this->nombre','$this->apellido','$this->tipo', '$this->subTipo', '$this->sector', '$this->email', '$this->password', '$this->token', '$this->fechaRegistro')");
		$consulta->execute();
		return $objetoAccesoDato->RetornarUltimoIdInsertado();
	}
    public static function TraerTodoLosUsuarios_EnArray()
	{
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select id as id, nombre as nombre, apellido as apellido, tipo as tipo, sub_tipo as subTipo, sector as sector, email as email, contraseña as password, token as token, fecha_registro as fechaRegistro from usuarios");
        $consulta->execute();
        $usuarios = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $usuario = array($i->id, $i->nombre, $i->apellido, $i->tipo, $i->subTipo, $i->sector, $i->email, $i->password,$i->fechaRegistro);
            $usuarios[] = $usuario;
        }
        return $usuarios;
	}
    public static function TraerTodoLosUsuarios()
	{
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select id as id, nombre as nombre, apellido as apellido, tipo as tipo, sub_tipo as subTipo, sector as sector, email as email, contraseña as password, token as token, fecha_registro as fechaRegistro from usuarios");
        $consulta->execute();
        $arrayObtenido = array();
        $usuarios = array();
        $arrayObtenido = $consulta->fetchAll(PDO::FETCH_OBJ);
        foreach($arrayObtenido as $i){
            $usuario = new Usuario($i->nombre, $i->apellido, $i->tipo, $i->email, $i->password, $i->subTipo, $i->sector,$i->fechaRegistro, $i->id , $i->token);
            $usuarios[] = $usuario;
        }
        return $usuarios;
	}
    public static function TraerUnUsuarioId($id) 
	{
        $usuario = null;
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select * from usuarios where id = ?");
        $consulta->bindValue(1, $id, PDO::PARAM_INT);
        $consulta->execute();
        $usuarioBuscado= $consulta->fetchObject();
        if($usuarioBuscado != null){
            $usuario = new Usuario($usuarioBuscado->nombre, $usuarioBuscado->apellido, $usuarioBuscado->tipo, $usuarioBuscado->email, $usuarioBuscado->contraseña, $usuarioBuscado->sub_tipo, $usuarioBuscado->sector,$usuarioBuscado->fecha_registro, $usuarioBuscado->id ,  $usuarioBuscado->token);
        }
        return $usuario;
	}
    public static function TraerUnUsuarioEmail($email) 
	{
        $usuario = null;
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("select * from usuarios where email = ?");
        $consulta->bindValue(1, $email, PDO::PARAM_STR);
        $consulta->execute();
        $usuarioBuscado= $consulta->fetchObject();
        
        if($usuarioBuscado != null){
            $usuario = new Usuario($usuarioBuscado->nombre, $usuarioBuscado->apellido, $usuarioBuscado->tipo, $usuarioBuscado->email, $usuarioBuscado->contraseña, $usuarioBuscado->sub_tipo, $usuarioBuscado->sector,$usuarioBuscado->fecha_registro, $usuarioBuscado->id ,  $usuarioBuscado->token);
        }
        return $usuario;
	}
    public function ModificarTokenDB($data){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("update usuarios set token = ? where id = ?");
        $consulta->bindValue(1, $data["token"], PDO::PARAM_STR);
        $consulta->bindValue(2, $this->id, PDO::PARAM_INT);
        return$consulta->execute();
    }
    public static function FiltrarParaMostrar($array){
        if(count($array) > 0){
            foreach($array as $i){
                unset($i->password);
                unset($i->token);
            }
            return $array;
        }
    }
    public static function FiltrarParaGuardar($array){
        if(count($array) > 0){
            foreach($array as $i){
                unset($i['password']);
                unset($i['token']);
            }
            return $array;
        }
    }
}

?>