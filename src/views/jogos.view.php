<?php
$_tipoAcao = isset($_POST['_tipoAcao']) ? $_POST['_tipoAcao'] : null;
$msg='';
if($_tipoAcao==='add_jogo'){adicionarJogo($connection,$_POST);$msg="Jogo adicionado!";}
elseif($_tipoAcao==='resultado'){registrarResultado($connection,$_POST['jogo_id'],intval($_POST['gols1']),intval($_POST['gols2']));$msg="Resultado registrado!";}
elseif($_tipoAcao==='excluir_jogo'){excluirJogo($connection,$_POST['id']);$msg="Jogo excluido!";}
$fase=isset($_GET['fase'])?$_GET['fase']:'todos';
$jogos=obterJogos($connection,$fase);
?>
<head><link href="src/css/bolao.css" rel="stylesheet" type="text/css"></head>

<div class="bolao-wrapper">
    <div class="bolao-header">
        <h2>&#9917; JOGOS</h2>
        <p>Gerenciar jogos e resultados</p>
    </div>

    <?php if($msg): ?>
    <div class="bolao-msg bolao-msg-success">&#10004; <?php echo $msg; ?></div>
    <?php endif; ?>

    <div class="bolao-nav">
        <a href="?">&#127942; Ranking</a>
        <a href="?_route=jogos" class="active">&#9917; Jogos</a>
        <a href="?_route=palpites">&#128221; Palpites</a>
        <a href="?_route=participantes">&#128101; Participantes</a>
        <a href="?_route=config">&#9881; Config</a>
    </div>

    <!-- Adicionar Jogo -->
    <div class="bolao-card">
        <div class="bolao-card-header">&#10133; ADICIONAR JOGO</div>
        <div class="bolao-card-body">
            <form method="post" class="bolao-form-inline">
                <input type="hidden" name="_tipoAcao" value="add_jogo">
                <select name="fase" class="bolao-select">
                    <option value="grupos">Grupos</option>
                    <option value="oitavas">Oitavas</option>
                    <option value="quartas">Quartas</option>
                    <option value="semi">Semi</option>
                    <option value="final">Final</option>
                </select>
                <input name="grupo" placeholder="Grupo" class="bolao-input" style="width:70px;">
                <input name="time1" placeholder="Time 1" required class="bolao-input" style="width:120px;">
                <input name="time2" placeholder="Time 2" required class="bolao-input" style="width:120px;">
                <input name="data_jogo" type="datetime-local" required class="bolao-input">
                <input name="local_jogo" placeholder="Local" class="bolao-input" style="width:130px;">
                <button type="submit" class="bolao-btn bolao-btn-primary">Adicionar</button>
            </form>
        </div>
    </div>

    <!-- Tabela de Jogos -->
    <div class="bolao-card">
        <div class="bolao-card-header">&#128203; LISTA DE JOGOS (<?php echo count($jogos); ?>)</div>
        <div class="bolao-card-body" style="padding:0;overflow-x:auto;">
            <table class="bolao-table">
                <thead>
                    <tr>
                        <th>DATA</th>
                        <th>FASE</th>
                        <th>JOGO</th>
                        <th style="text-align:center;">PLACAR</th>
                        <th>LOCAL</th>
                        <th style="text-align:center;width:40px;">X</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($jogos)): ?>
                <tr><td colspan="6" class="bolao-empty">Nenhum jogo cadastrado</td></tr>
                <?php else: foreach($jogos as $j): ?>
                <tr style="<?php echo $j['finalizado']?'background:#f9fbe7;':''; ?>">
                    <td style="padding:10px 12px;border-bottom:1px solid #f1f5f9;"><?php echo date('d/m H:i',strtotime($j['data_jogo'])); ?></td>
                    <td style="padding:10px 12px;border-bottom:1px solid #f1f5f9;">
                        <span class="bolao-badge bolao-badge-fase"><?php echo strtoupper($j['fase']).' '.$j['grupo']; ?></span>
                    </td>
                    <td style="padding:10px 12px;border-bottom:1px solid #f1f5f9;font-weight:700;"><?php echo $j['time1'].' x '.$j['time2']; ?></td>
                    <td style="padding:10px 12px;border-bottom:1px solid #f1f5f9;text-align:center;">
                        <?php if($j['finalizado']): ?>
                        <form method="post" class="placar-form">
                            <input type="hidden" name="_tipoAcao" value="resultado">
                            <input type="hidden" name="jogo_id" value="<?php echo $j['id']; ?>">
                            <span class="placar-set">
                                <input type="number" name="gols1" min="0" value="<?php echo $j['gols1']; ?>">
                            </span>
                            <strong style="color:#999;">-</strong>
                            <span class="placar-set">
                                <input type="number" name="gols2" min="0" value="<?php echo $j['gols2']; ?>">
                            </span>
                            <button type="submit" class="bolao-btn bolao-btn-warning bolao-btn-sm">Editar</button>
                        </form>
                        <?php else: ?>
                        <form method="post" class="placar-form">
                            <input type="hidden" name="_tipoAcao" value="resultado">
                            <input type="hidden" name="jogo_id" value="<?php echo $j['id']; ?>">
                            <input type="number" name="gols1" min="0" value="0">
                            <strong style="color:#ccc;">-</strong>
                            <input type="number" name="gols2" min="0" value="0">
                            <button type="submit" class="bolao-btn bolao-btn-primary bolao-btn-sm">OK</button>
                        </form>
                        <?php endif; ?>
                    </td>
                    <td style="padding:10px 12px;border-bottom:1px solid #f1f5f9;font-size:11px;color:#718096;"><?php echo $j['local_jogo']; ?></td>
                    <td style="padding:10px 12px;border-bottom:1px solid #f1f5f9;text-align:center;">
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="_tipoAcao" value="excluir_jogo">
                            <input type="hidden" name="id" value="<?php echo $j['id']; ?>">
                            <button type="submit" onclick="return confirm('Excluir este jogo?')" class="bolao-btn-icon">&#128465;</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
