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
<h2 align="center" style="padding:12px 0 5px;font-weight:700;">&#9917; JOGOS</h2>
<?php if($msg):?><div style="background:#e8f5e9;border:1px solid #a5d6a7;color:#2e7d32;padding:10px;border-radius:6px;margin:10px 15px;text-align:center;font-size:13px;"><?php echo $msg;?></div><?php endif;?>
<div style="padding:0 15px;">
    <div style="display:flex;gap:6px;margin-bottom:15px;flex-wrap:wrap;">
        <a href="?" style="padding:8px 14px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:#555;font-size:13px;background:#f8f8f8;">Ranking</a>
        <a href="?_route=jogos" style="padding:8px 14px;border:1px solid #2e7d32;border-radius:6px;text-decoration:none;color:white;background:#2e7d32;font-size:13px;">Jogos</a>
        <a href="?_route=palpites" style="padding:8px 14px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:#555;font-size:13px;background:#f8f8f8;">Palpites</a>
        <a href="?_route=participantes" style="padding:8px 14px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:#555;font-size:13px;background:#f8f8f8;">Participantes</a>
        <a href="?_route=config" style="padding:8px 14px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:#555;font-size:13px;background:#f8f8f8;">Config</a>
    </div>

    <!-- Adicionar Jogo -->
    <div style="background:#fff;border:1px solid #e0e0e0;border-radius:8px;margin-bottom:15px;overflow:hidden;">
        <div style="background:#f5f5f5;padding:10px 15px;font-weight:700;font-size:13px;border-bottom:1px solid #e0e0e0;">ADICIONAR JOGO</div>
        <div style="padding:12px;">
            <form method="post" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                <input type="hidden" name="_tipoAcao" value="add_jogo">
                <select name="fase" style="height:34px;padding:0 8px;border:1px solid #ddd;border-radius:4px;"><option value="grupos">Grupos</option><option value="oitavas">Oitavas</option><option value="quartas">Quartas</option><option value="semi">Semi</option><option value="final">Final</option></select>
                <input name="grupo" placeholder="Grupo" style="width:60px;height:34px;padding:0 8px;border:1px solid #ddd;border-radius:4px;">
                <input name="time1" placeholder="Time 1" required style="height:34px;padding:0 8px;border:1px solid #ddd;border-radius:4px;">
                <input name="time2" placeholder="Time 2" required style="height:34px;padding:0 8px;border:1px solid #ddd;border-radius:4px;">
                <input name="data_jogo" type="datetime-local" required style="height:34px;padding:0 8px;border:1px solid #ddd;border-radius:4px;">
                <input name="local_jogo" placeholder="Local" style="height:34px;padding:0 8px;border:1px solid #ddd;border-radius:4px;">
                <button type="submit" style="height:34px;padding:0 16px;background:#2e7d32;color:white;border:none;border-radius:4px;cursor:pointer;">Adicionar</button>
            </form>
        </div>
    </div>

    <!-- Tabela de Jogos -->
    <div style="overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;font-size:12px;background:white;">
        <thead><tr style="background:#f8f8f8;"><th style="padding:8px;border-bottom:2px solid #e0e0e0;">DATA</th><th style="padding:8px;border-bottom:2px solid #e0e0e0;">FASE</th><th style="padding:8px;border-bottom:2px solid #e0e0e0;">JOGO</th><th style="padding:8px;border-bottom:2px solid #e0e0e0;">PLACAR</th><th style="padding:8px;border-bottom:2px solid #e0e0e0;">LOCAL</th><th style="padding:8px;border-bottom:2px solid #e0e0e0;">ACAO</th></tr></thead>
        <tbody>
        <?php if(empty($jogos)):?><tr><td colspan="6" style="text-align:center;padding:30px;color:#999;">Nenhum jogo</td></tr>
        <?php else: foreach($jogos as $j):?>
        <tr style="<?php echo $j['finalizado']?'background:#f9fbe7;':'';?>">
            <td style="padding:8px;border-bottom:1px solid #f0f0f0;"><?php echo date('d/m H:i',strtotime($j['data_jogo']));?></td>
            <td style="padding:8px;border-bottom:1px solid #f0f0f0;"><span style="background:#e3f2fd;color:#1565c0;padding:2px 8px;border-radius:4px;font-size:10px;font-weight:700;"><?php echo strtoupper($j['fase']).' '.$j['grupo'];?></span></td>
            <td style="padding:8px;border-bottom:1px solid #f0f0f0;font-weight:600;"><?php echo $j['time1'].' x '.$j['time2'];?></td>
            <td style="padding:8px;border-bottom:1px solid #f0f0f0;text-align:center;">
                <?php if($j['finalizado']):?><span style="background:#2e7d32;color:white;padding:2px 10px;border-radius:4px;font-weight:700;"><?php echo $j['gols1'].' - '.$j['gols2'];?></span>
                <?php else:?><form method="post" style="display:inline-flex;gap:4px;align-items:center;"><input type="hidden" name="_tipoAcao" value="resultado"><input type="hidden" name="jogo_id" value="<?php echo $j['id'];?>"><input type="number" name="gols1" min="0" value="0" style="width:40px;height:28px;text-align:center;border:1px solid #ddd;border-radius:4px;">-<input type="number" name="gols2" min="0" value="0" style="width:40px;height:28px;text-align:center;border:1px solid #ddd;border-radius:4px;"><button type="submit" style="height:28px;padding:0 8px;background:#2e7d32;color:white;border:none;border-radius:4px;cursor:pointer;">OK</button></form><?php endif;?>
            </td>
            <td style="padding:8px;border-bottom:1px solid #f0f0f0;font-size:11px;"><?php echo $j['local_jogo'];?></td>
            <td style="padding:8px;border-bottom:1px solid #f0f0f0;"><form method="post" style="display:inline;"><input type="hidden" name="_tipoAcao" value="excluir_jogo"><input type="hidden" name="id" value="<?php echo $j['id'];?>"><button type="submit" onclick="return confirm('Excluir?')" style="background:none;border:none;cursor:pointer;font-size:14px;">&#128465;</button></form></td>
        </tr>
        <?php endforeach;endif;?>
        </tbody>
    </table>
    </div>
</div>
