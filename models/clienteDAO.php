<?php
include("conexion.php");

function insertar($dni,$ape,$nom,$ema,$cel,$dir){
    global $cn;
    $sql="INSERT INTO tcliente(dni,apellidos,nombres,email,celular,direccion) VALUES ('".$dni."','".$ape."','".$nom."','".$ema."','".$cel."','".$dir."')";
    if(mysqli_query($cn,$sql)){
        return "Los datos fueron registrados con exito!!!";
    }else{
        return "ERROR, los datos no se registraron";
    }
}

function getClientes(){
    global $cn;
    $sql="SELECT * FROM tcliente ORDER BY id";
    $res=mysqli_query($cn,$sql);
    if(!$res){
        throw new RuntimeException("Error al listar clientes: ".mysqli_error($cn));
    }
    return $res;
}

function consultarCliente($idC){
    global $cn;
    $sql="SELECT * FROM tcliente WHERE id='".$idC."'";
    $res=mysqli_query($cn,$sql);
    if(!$res){
        throw new RuntimeException("Error al consultar el cliente: ".mysqli_error($cn));
    }
    return $res;
}

function modificar($id,$dni,$ape,$nom,$ema,$cel,$dir){
    global $cn;
    $sql="UPDATE tcliente SET dni='".$dni."', apellidos='".$ape."', nombres='".$nom."', email='".$ema."', celular='".$cel."', direccion='".$dir."' WHERE id='".$id."'";
    if(mysqli_query($cn,$sql)){
        return "Los datos fueron modificados de manera correcta";
    }else{
        return "ERROR, No se pudo actualizar los datos";
    }
}

function eliminarCliente($id){
    global $cn;
    $sql="DELETE FROM tcliente WHERE id='".$id."'";
    if(mysqli_query($cn,$sql)){
        return "El cliente fue eliminado con Exito!!!";
    }else{
        return "ERROR, El cliente No fue eliminado";
    }
}
?>
