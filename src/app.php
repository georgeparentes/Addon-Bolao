<?php
include(__DIR__ . '/config/database.php');
include(__DIR__ . '/models/bolao.model.php');
criarTabelas($connection);

// AJAX
if (isset($_GET['_ajax'])) {
    header('Content-Type: application/json; charset=utf-8');
    $acao = $_GET['_ajax'];
    if ($acao === 'ranking') echo json_encode(obterRanking($connection));
    elseif ($acao === 'jogos') echo json_encode(obterJogos($connection, $_GET['fase'] ?? 'todos'));
    exit;
}

$get_route = isset($_REQUEST['_route']) ? $_REQUEST['_route'] : null;

switch($get_route) {
    case "jogos": include(__DIR__ . '/views/jogos.view.php'); break;
    case "palpites": include(__DIR__ . '/views/palpites.view.php'); break;
    case "participantes": include(__DIR__ . '/views/participantes.view.php'); break;
    case "acertos": include(__DIR__ . '/views/acertos.view.php'); break;
    case "acessos": include(__DIR__ . '/views/acessos.view.php'); break;
    case "config": include(__DIR__ . '/views/config.view.php'); break;
    default: include(__DIR__ . '/views/inicio.view.php'); break;
}
