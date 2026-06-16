<?php
$_tipoAcao = isset($_POST['_tipoAcao']) ? $_POST['_tipoAcao'] : null;
$msg='';
if($_tipoAcao==='add_jogo'){adicionarJogo($connection,$_POST);$msg="Jogo adicionado!";}
elseif($_tipoAcao==='resultado'){registrarResultado($connection,$_POST['jogo_id'],intval($_POST['gols1']),intval($_POST['gols2']));$msg="Resultado registrado!";}
elseif($_tipoAcao==='excluir_jogo'){excluirJogo($connection,$_POST['id']);$msg="Jogo excluido!";}
$fase=isset($_GET['fase'])?$_GET['fase']:'todos';
$jogos=obterJogos($connection,$fase);
?>
<?php include(__DIR__ . '/_css.php'); ?>

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
        <a href="?_route=acessos">&#128202; Acessos</a>
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

    <!-- Lista de Jogos -->
    <div class="bolao-card">
        <div class="bolao-card-header">&#128203; LISTA DE JOGOS (<?php echo count($jogos); ?>)</div>
        <div class="bolao-card-body" style="padding:8px!important;">
            <?php if(empty($jogos)): ?>
            <div class="bolao-empty">Nenhum jogo cadastrado</div>
            <?php else: foreach($jogos as $j): ?>
            <div style="border:1px solid #e2e8f0;border-radius:8px;padding:10px;margin-bottom:8px;background:<?php echo $j['finalizado']?'#f9fbe7':'#fff'; ?>;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                    <span style="font-size:10px;color:#718096;"><?php echo date('d/m H:i',strtotime($j['data_jogo'])); ?></span>
                    <span class="bolao-badge bolao-badge-fase"><?php echo strtoupper($j['fase']).' '.$j['grupo']; ?></span>
                    <form method="post" style="display:inline;margin:0;">
                        <input type="hidden" name="_tipoAcao" value="excluir_jogo">
                        <input type="hidden" name="id" value="<?php echo $j['id']; ?>">
                        <button type="submit" onclick="return confirm('Excluir?')" class="bolao-btn-icon" style="font-size:12px!important;">&#128465;</button>
                    </form>
                </div>
                <div style="font-weight:700;font-size:14px;text-align:center;margin-bottom:4px;"><?php echo bandeiraPais($j['time1']).' '.$j['time1']; ?> x <?php echo $j['time2'].' '.bandeiraPais($j['time2']); ?></div>
                <?php if($j['local_jogo']): ?><div style="font-size:9px;color:#718096;text-align:center;margin-bottom:6px;"><?php echo $j['local_jogo']; ?></div><?php endif; ?>
                <div style="text-align:center;">
                    <?php if($j['finalizado']): ?>
                    <form method="post" style="display:inline-flex;align-items:center;justify-content:center;gap:6px;">
                        <input type="hidden" name="_tipoAcao" value="resultado">
                        <input type="hidden" name="jogo_id" value="<?php echo $j['id']; ?>">
                        <input type="number" name="gols1" min="0" value="<?php echo $j['gols1']; ?>" style="width:50px;height:38px;text-align:center;border:2px solid #2e7d32;border-radius:8px;font-size:18px;font-weight:800;background:#f1f8e9;outline:none;">
                        <strong style="color:#999;font-size:16px;">-</strong>
                        <input type="number" name="gols2" min="0" value="<?php echo $j['gols2']; ?>" style="width:50px;height:38px;text-align:center;border:2px solid #2e7d32;border-radius:8px;font-size:18px;font-weight:800;background:#f1f8e9;outline:none;">
                        <button type="submit" class="bolao-btn bolao-btn-warning bolao-btn-sm">Editar</button>
                    </form>
                    <?php else: ?>
                    <form method="post" style="display:inline-flex;align-items:center;justify-content:center;gap:6px;">
                        <input type="hidden" name="_tipoAcao" value="resultado">
                        <input type="hidden" name="jogo_id" value="<?php echo $j['id']; ?>">
                        <input type="number" name="gols1" min="0" value="0" style="width:50px;height:38px;text-align:center;border:2px solid #e2e8f0;border-radius:8px;font-size:18px;font-weight:800;background:#fff;outline:none;">
                        <strong style="color:#ccc;font-size:16px;">-</strong>
                        <input type="number" name="gols2" min="0" value="0" style="width:50px;height:38px;text-align:center;border:2px solid #e2e8f0;border-radius:8px;font-size:18px;font-weight:800;background:#fff;outline:none;">
                        <button type="submit" class="bolao-btn bolao-btn-primary bolao-btn-sm" style="height:38px!important;padding:0 14px!important;font-size:12px!important;">OK</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>
