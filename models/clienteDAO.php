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
    $sql="SELECT * FROM tcliente";
    return mysqli_query($cn,$sql);
}

function consultarCliente($idC){
    global $cn;
    $sql="SELECT * FROM tcliente WHERE id='".$idC."'";
    return mysqli_query($cn,$sql);
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
