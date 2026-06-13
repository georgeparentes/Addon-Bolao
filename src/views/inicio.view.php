<?php
$_tipoAcao = isset($_POST['_tipoAcao']) ? $_POST['_tipoAcao'] : null;
$msg = '';
if ($_tipoAcao==='importar_jogos') { $q=importarJogosCopa2026($connection); $msg=$q>0?"$q jogos importados!":"Jogos ja importados."; }
elseif ($_tipoAcao==='resultado') { registrarResultado($connection,$_POST['jogo_id'],intval($_POST['gols1']),intval($_POST['gols2'])); $msg="Resultado registrado!"; }

$ranking = obterRanking($connection);
$jogos = obterJogos($connection,'todos');
$proximos = array_values(array_filter($jogos, function($j){return !$j['finalizado'];}));
?>
<head><link href="src/css/bolao.css" rel="stylesheet" type="text/css"></head>

<h2 align="center" style="padding:12px 0 2px; font-weight:700; font-size:18px;">&#9917; <?php echo $Manifest->{'name'}; ?> V <?php echo $Manifest->{'version'}; ?></h2>
<p align="center" style="font-size:12px; color:#777; margin-bottom:10px;">Copa do Mundo FIFA 2026</p>

<?php if($msg): ?><div style="background:#e8f5e9;border:1px solid #a5d6a7;color:#2e7d32;padding:10px;border-radius:6px;margin:10px 15px;text-align:center;font-size:13px;"><?php echo $msg; ?></div><?php endif; ?>

<div style="padding:0 15px;">
    <div style="display:flex;gap:6px;margin-bottom:15px;flex-wrap:wrap;">
        <a href="?" style="padding:8px 14px;border:1px solid #2e7d32;border-radius:6px;text-decoration:none;color:white;background:#2e7d32;font-size:13px;font-weight:500;">Ranking</a>
        <a href="?_route=jogos" style="padding:8px 14px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:#555;font-size:13px;background:#f8f8f8;">Jogos</a>
        <a href="?_route=palpites" style="padding:8px 14px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:#555;font-size:13px;background:#f8f8f8;">Palpites</a>
        <a href="?_route=participantes" style="padding:8px 14px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:#555;font-size:13px;background:#f8f8f8;">Participantes</a>
        <a href="?_route=config" style="padding:8px 14px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:#555;font-size:13px;background:#f8f8f8;">Config</a>
    </div>

    <div style="display:flex;gap:15px;flex-wrap:wrap;">
        <!-- RANKING -->
        <div style="flex:2;min-width:300px;background:#fff;border:1px solid #e0e0e0;border-radius:8px;overflow:hidden;">
            <div style="background:#f5f5f5;padding:10px 15px;font-weight:700;font-size:13px;border-bottom:1px solid #e0e0e0;">&#127942; RANKING</div>
            <div style="padding:10px;">
                <?php if(empty($ranking)): ?>
                <p style="text-align:center;color:#999;padding:20px;">Nenhum participante. <a href="?_route=participantes">Cadastrar</a></p>
                <?php else: ?>
                <table style="width:100%;border-collapse:collapse;font-size:12px;">
                    <thead><tr style="background:#f8f8f8;"><th style="padding:8px;text-align:left;border-bottom:2px solid #e0e0e0;">#</th><th style="padding:8px;text-align:left;border-bottom:2px solid #e0e0e0;">PARTICIPANTE</th><th style="padding:8px;text-align:center;border-bottom:2px solid #e0e0e0;">PTS</th></tr></thead>
                    <tbody>
                    <?php $p=0; foreach($ranking as $r): $p++; $bg=$p==1?'#fff8e1':($p==2?'#f5f5f5':($p==3?'#fff3e0':'')); ?>
                    <tr style="background:<?php echo $bg; ?>"><td style="padding:8px;font-weight:700;"><?php echo $p==1?'&#129351;':($p==2?'&#129352;':($p==3?'&#129353;':$p.'o')); ?></td><td style="padding:8px;"><strong><?php echo htmlspecialchars($r['nome']); ?></strong></td><td style="padding:8px;text-align:center;font-weight:700;font-size:16px;color:#2e7d32;"><?php echo $r['pontos_total']; ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- PRÓXIMOS JOGOS -->
        <div style="flex:1;min-width:250px;background:#fff;border:1px solid #e0e0e0;border-radius:8px;overflow:hidden;">
            <div style="background:#f5f5f5;padding:10px 15px;font-weight:700;font-size:13px;border-bottom:1px solid #e0e0e0;">&#128197; PROXIMOS JOGOS</div>
            <div style="padding:10px;">
                <?php if(empty($proximos)): ?>
                <form method="post" style="text-align:center;padding:15px;"><input type="hidden" name="_tipoAcao" value="importar_jogos"><button type="submit" style="background:#2e7d32;color:white;border:none;padding:10px 20px;border-radius:6px;font-size:13px;cursor:pointer;">Importar Jogos Copa 2026</button></form>
                <?php else: foreach(array_slice($proximos,0,5) as $j): ?>
                <div style="padding:8px;border:1px solid #eee;border-radius:6px;margin-bottom:6px;text-align:center;">
                    <div style="font-size:11px;color:#888;"><?php echo date('d/m H:i',strtotime($j['data_jogo'])); ?></div>
                    <div style="font-size:14px;font-weight:600;"><?php echo $j['time1']; ?> <span style="color:#999;">x</span> <?php echo $j['time2']; ?></div>
                    <div style="font-size:10px;color:#aaa;"><?php echo $j['local_jogo']; ?></div>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>

    <!-- REGRAS -->
    <div style="margin-top:15px;background:#fff;border:1px solid #e0e0e0;border-radius:8px;overflow:hidden;">
        <div style="background:#f5f5f5;padding:10px 15px;font-weight:700;font-size:13px;border-bottom:1px solid #e0e0e0;">&#128203; PONTUACAO</div>
        <div style="padding:15px;display:flex;gap:12px;flex-wrap:wrap;justify-content:center;">
            <span style="padding:8px 14px;background:#f8f8f8;border-radius:6px;border:1px solid #eee;"><strong style="font-size:18px;color:#2e7d32;">10</strong> Placar exato</span>
            <span style="padding:8px 14px;background:#f8f8f8;border-radius:6px;border:1px solid #eee;"><strong style="font-size:18px;color:#2e7d32;">7</strong> Vencedor+saldo</span>
            <span style="padding:8px 14px;background:#f8f8f8;border-radius:6px;border:1px solid #eee;"><strong style="font-size:18px;color:#2e7d32;">5</strong> Vencedor</span>
            <span style="padding:8px 14px;background:#f8f8f8;border-radius:6px;border:1px solid #eee;"><strong style="font-size:18px;color:#2e7d32;">2</strong> 1 gol certo</span>
            <span style="padding:8px 14px;background:#f8f8f8;border-radius:6px;border:1px solid #eee;"><strong style="font-size:18px;color:#999;">0</strong> Errou</span>
        </div>
    </div>
</div>
