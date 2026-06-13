<?php
// INCLUE FUNCOES DE ADDONS -----------------------------------------------------------------------
require_once 'addons.class.php';
// $Manifest é definido pelo addons.class.php (copiado do MAPA)
if (!isset($Manifest)) {
    $Manifest = json_decode(file_get_contents(__DIR__ . '/manifest.json'));
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="has-navbar-fixed-top">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <title>MK-AUTH :: <?php echo $Manifest->{'name'}; ?></title>

    <link href="../../estilos/mk-auth.css" rel="stylesheet" type="text/css" />
    <link href="../../estilos/font-awesome.css" rel="stylesheet" type="text/css" />
    <link href="../../estilos/bi-icons.css" rel="stylesheet" type="text/css" />

    <script src="../../scripts/jquery.js"></script>
    <script src="../../scripts/mk-auth.js"></script>
</head>

<body>

    <?php include('../../topo.php'); ?>

    <nav class="breadcrumb has-bullet-separator is-centered" aria-label="breadcrumbs"> </nav>

    <?php include('src/app.php'); ?>

    <?php include('../../baixo.php'); ?>

    <script src="../../menu.js.hhvm"></script>

</body>

</html>
<?php ob_end_flush(); ?>
