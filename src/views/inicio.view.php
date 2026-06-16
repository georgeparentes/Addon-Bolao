<?php
$_tipoAcao = isset($_POST['_tipoAcao']) ? $_POST['_tipoAcao'] : null;
$msg = '';
if ($_tipoAcao==='importar_jogos') { $q=importarJogosCopa2026($connection); $msg=$q>0?"$q jogos importados!":"Jogos ja importados."; }
elseif ($_tipoAcao==='resultado') { registrarResultado($connection,$_POST['jogo_id'],intval($_POST['gols1']),intval($_POST['gols2'])); $msg="Resultado registrado!"; }

$ranking = obterRanking($connection);
$jogos = obterJogos($connection,'todos');
$proximos = array_values(array_filter($jogos, function($j){return !$j['finalizado'];}));
?>
<?php include(__DIR__ . '/_css.php'); ?>

<div class="bolao-wrapper">
    <div class="bolao-header">
        <h2>&#9917; <?php echo $Manifest->{'name'}; ?> V <?php echo $Manifest->{'version'}; ?></h2>
        <p>Copa do Mundo FIFA 2026 &bull; EUA &bull; M&eacute;xico &bull; Canad&aacute;</p>
    </div>

    <?php if($msg): ?>
    <div class="bolao-msg bolao-msg-success">&#10004; <?php echo $msg; ?></div>
    <?php endif; ?>

    <div class="bolao-nav">
        <a href="?" class="active">&#127942; Ranking</a>
        <a href="?_route=jogos">&#9917; Jogos</a>
        <a href="?_route=palpites">&#128221; Palpites</a>
        <a href="?_route=participantes">&#128101; Participantes</a>
        <a href="?_route=acessos">&#128202; Acessos</a>
        <a href="?_route=config">&#9881; Config</a>
    </div>

    <div class="bolao-grid">
        <!-- RANKING -->
        <div class="col-main">
            <div class="bolao-card">
                <div class="bolao-card-header">&#127942; RANKING GERAL</div>
                <div class="bolao-card-body" style="padding:0;">
                    <?php if(empty($ranking)): ?>
                    <div class="bolao-empty">
                        <p>Nenhum participante cadastrado.</p>
                        <a href="?_route=participantes">Cadastrar participantes</a>
                    </div>
                    <?php else: ?>
                    <table class="bolao-table">
                        <thead>
                            <tr>
                                <th style="width:50px;">#</th>
                                <th>PARTICIPANTE</th>
                                <th style="width:80px;">ACERTOS</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $p=0; foreach($ranking as $r): $p++; ?>
                        <tr>
                            <td>
                                <span class="rank-pos <?php echo $p<=3?'rank-'.$p:'rank-other'; ?>">
                                    <?php echo $p==1?'&#129351;':($p==2?'&#129352;':($p==3?'&#129353;':$p.'&ordm;')); ?>
                                </span>
                            </td>
                            <td><span class="rank-name"><?php echo htmlspecialchars($r['nome']); ?></span></td>
                            <td><span class="rank-pts"><?php echo $r['pontos_total']; ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- PR&Oacute;XIMOS JOGOS -->
        <div class="col-side">
            <div class="bolao-card">
                <div class="bolao-card-header">&#128197; PR&Oacute;XIMOS JOGOS</div>
                <div class="bolao-card-body">
                    <?php if(empty($proximos)): ?>
                    <form method="post" style="text-align:center;padding:10px;">
                        <input type="hidden" name="_tipoAcao" value="importar_jogos">
                        <button type="submit" class="bolao-btn bolao-btn-primary bolao-btn-full">&#9917; Importar Jogos Copa 2026</button>
                    </form>
                    <?php else: foreach(array_slice($proximos,0,5) as $j): ?>
                    <div class="jogo-card">
                        <div class="jogo-data"><?php echo date('d/m H:i',strtotime($j['data_jogo'])); ?></div>
                        <div class="jogo-times"><?php echo bandeiraPais($j['time1']).' '.$j['time1']; ?><span class="vs">x</span><?php echo $j['time2'].' '.bandeiraPais($j['time2']); ?></div>
                        <div class="jogo-local"><?php echo $j['local_jogo']; ?></div>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- REGRAS -->
    <div class="bolao-card">
        <div class="bolao-card-header">&#128203; REGRA DO BOLAO</div>
        <div class="bolao-regras">
            <strong>Acertou o placar exato = 1 ponto (acerto)</strong>
            <span>Ganha quem acertar mais resultados</span>
        </div>
    </div>
</div>
