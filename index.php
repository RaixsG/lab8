<?php
$m="";
if(isset($_GET['m'])){
    $m="?m=".urlencode($_GET['m']);
}
header("Location: views/frmListarCliente.php".$m);
?>
