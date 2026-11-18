<?php
include("../models/clienteDAO.php");

function validarCamposCliente($dni, $ape, $nom, $ema, $cel, $dir){
    $campos = [$dni, $ape, $nom, $ema, $cel, $dir];
    foreach ($campos as $valor) {
        if (trim((string)$valor) === '') {
            return "ERROR, todos los campos son obligatorios";
        }
    }
    if (!preg_match('/^\d{8}$/', trim((string)$dni))) {
        return "ERROR, el DNI debe tener 8 dígitos";
    }
    if (!preg_match('/^\d{9}$/', trim((string)$cel))) {
        return "ERROR, el celular debe tener 9 dígitos";
    }
    if (!filter_var(trim((string)$ema), FILTER_VALIDATE_EMAIL)) {
        return "ERROR, el email no tiene un formato válido";
    }
    return null;
}

if(isset($_POST['btnActualizar'])){//Modificar
    $id=$_POST['id'];
    $dni=trim((string)$_POST['dni']);
    $ape=trim((string)$_POST['apellidos']);
    $nom=trim((string)$_POST['nombres']);
    $ema=trim((string)$_POST['email']);
    $cel=trim((string)$_POST['celular']);
    $dir=trim((string)$_POST['direccion']);
    $errorValidacion = validarCamposCliente($dni,$ape,$nom,$ema,$cel,$dir);
    $m = $errorValidacion !== null ? $errorValidacion : modificar($id,$dni,$ape,$nom,$ema,$cel,$dir);
    header("Location: ../index.php?m=".urlencode($m));
}else if(isset($_GET['id'])){//Eliminar
    $id=$_GET['id'];
    $m=eliminarCliente($id);
    header("Location: ../index.php?m=".urlencode($m));
}else if(isset($_POST['btnRegistrar'])){//Registrar
    $dni=trim((string)$_POST['dni']);
    $ape=trim((string)$_POST['apellidos']);
    $nom=trim((string)$_POST['nombres']);
    $ema=trim((string)$_POST['email']);
    $cel=trim((string)$_POST['celular']);
    $dir=trim((string)$_POST['direccion']);
    $errorValidacion = validarCamposCliente($dni,$ape,$nom,$ema,$cel,$dir);
    $m = $errorValidacion !== null ? $errorValidacion : insertar($dni,$ape,$nom,$ema,$cel,$dir);
    header("Location: ../index.php?m=".urlencode($m));
}

function consultarCli($idC){
    return consultarCliente($idC);
}

function listaClientes(){
    return getClientes();
}
?>
