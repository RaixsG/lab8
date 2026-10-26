<?php
include("../models/clienteDAO.php");

if(isset($_POST['btnActualizar'])){//Modificar
    $id=$_POST['id'];
    $dni=$_POST['dni'];
    $ape=$_POST['apellidos'];
    $nom=$_POST['nombres'];
    $ema=$_POST['email'];
    $cel=$_POST['celular'];
    $dir=$_POST['direccion'];
    $m=modificar($id,$dni,$ape,$nom,$ema,$cel,$dir);
    header("Location: ../index.php?m=$m");
}else if(isset($_GET['id'])){//Eliminar
    $id=$_GET['id'];
    $m=eliminarCliente($id);
    header("Location: ../index.php?m=$m");
}else if(isset($_POST['btnRegistrar'])){//Registrar
    $dni=$_POST['dni'];
    $ape=$_POST['apellidos'];
    $nom=$_POST['nombres'];
    $ema=$_POST['email'];
    $cel=$_POST['celular'];
    $dir=$_POST['direccion'];
    $m=insertar($dni,$ape,$nom,$ema,$cel,$dir);
    header("Location: ../index.php?m=$m");
}

function consultarCli($idC){
    return consultarCliente($idC);
}

function listaClientes(){
    return getClientes();
}
?>
