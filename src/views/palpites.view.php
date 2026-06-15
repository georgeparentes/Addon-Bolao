<?php
$_tipoAcao = isset($_POST['_tipoAcao']) ? $_POST['_tipoAcao'] : null;
$msg='';
if($_tipoAcao==='palpitar'){registrarPalpite($connection,$_POST['jogo_id'],$_POST['participante_id'],intval($_POST['gols1']),intval($_POST['gols2']));$msg="Palpite registrado!";}
$jogos=obterJogos($connection,'todos');
$abertos=array_values(array_filter($jogos,function($j){return !$j['finalizado'];}));
$participantes=listarParticipantes($connection);
?>
<head><link href="src/css/bolao.css" rel="stylesheet" type="text/css"></head>

<div class="bolao-wrapper">
    <div class="bolao-header">
        <h2>&#128221; PALPITES</h2>
        <p>Registrar palpites dos participantes</p>
    </div>

    <?php if($msg): ?>
    <div class="bolao-msg bolao-msg-success">&#10004; <?php echo $msg; ?></div>
    <?php endif; ?>

    <div class="bolao-nav">
        <a href="?">&#127942; Ranking</a>
        <a href="?_route=jogos">&#9917; Jogos</a>
        <a href="?_route=palpites" class="active">&#128221; Palpites</a>
        <a href="?_route=participantes">&#128101; Participantes</a>
        <a href="?_route=acessos">&#128202; Acessos</a>
        <a href="?_route=config">&#9881; Config</a>
    </div>

    <?php if(empty($participantes)): ?>
    <div class="bolao-card">
        <div class="bolao-card-body bolao-empty">
            <p>Cadastre participantes primeiro.</p>
            <a href="?_route=participantes">Ir para Participantes</a>
        </div>
    </div>
    <?php elseif(empty($abertos)): ?>
    <div class="bolao-card">
        <div class="bolao-card-body bolao-empty">
            <p>Nenhum jogo aberto para palpites.</p>
            <a href="?_route=jogos">Cadastrar jogos</a>
        </div>
    </div>
    <?php else: ?>
    <div class="bolao-card">
        <div class="bolao-card-header">&#128221; REGISTRAR PALPITE</div>
        <div class="bolao-card-body">
            <form method="post" class="bolao-form-inline">
                <input type="hidden" name="_tipoAcao" value="palpitar">
                <div>
                    <label class="bolao-label">Participante</label>
                    <select name="participante_id" required class="bolao-select">
                        <?php foreach($participantes as $p): ?>
                        <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['nome']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="bolao-label">Jogo</label>
                    <select name="jogo_id" required class="bolao-select">
                        <?php foreach($abertos as $j): ?>
                        <option value="<?php echo $j['id']; ?>"><?php echo $j['time1'].' x '.$j['time2'].' ('.date('d/m',strtotime($j['data_jogo'])).')'; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="bolao-label">Placar</label>
                    <div class="placar-form">
                        <input type="number" name="gols1" min="0" value="0">
                        <strong style="color:#999;">x</strong>
                        <input type="number" name="gols2" min="0" value="0">
                    </div>
                </div>
                <div style="padding-top:18px;">
                    <button type="submit" class="bolao-btn bolao-btn-primary">&#10004; Registrar</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>
