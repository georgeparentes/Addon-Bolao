<?php
$_tipoAcao = isset($_POST['_tipoAcao']) ? $_POST['_tipoAcao'] : null;
$msg='';
if($_tipoAcao==='palpitar'){registrarPalpite($connection,$_POST['jogo_id'],$_POST['participante_id'],intval($_POST['gols1']),intval($_POST['gols2']));$msg="Palpite registrado!";}
$jogos=obterJogos($connection,'todos');
$abertos=array_values(array_filter($jogos,function($j){return !$j['finalizado'];}));
$participantes=listarParticipantes($connection);
?>
<head><link href="src/css/bolao.css" rel="stylesheet" type="text/css"></head>
<h2 align="center" style="padding:12px 0 5px;font-weight:700;">&#128221; PALPITES</h2>
<?php if($msg):?><div style="background:#e8f5e9;border:1px solid #a5d6a7;color:#2e7d32;padding:10px;border-radius:6px;margin:10px 15px;text-align:center;font-size:13px;"><?php echo $msg;?></div><?php endif;?>
<div style="padding:0 15px;">
    <div style="display:flex;gap:6px;margin-bottom:15px;flex-wrap:wrap;">
        <a href="?" style="padding:8px 14px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:#555;font-size:13px;background:#f8f8f8;">Ranking</a>
        <a href="?_route=jogos" style="padding:8px 14px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:#555;font-size:13px;background:#f8f8f8;">Jogos</a>
        <a href="?_route=palpites" style="padding:8px 14px;border:1px solid #2e7d32;border-radius:6px;text-decoration:none;color:white;background:#2e7d32;font-size:13px;">Palpites</a>
        <a href="?_route=participantes" style="padding:8px 14px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:#555;font-size:13px;background:#f8f8f8;">Participantes</a>
        <a href="?_route=config" style="padding:8px 14px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:#555;font-size:13px;background:#f8f8f8;">Config</a>
    </div>

    <?php if(empty($participantes)):?>
    <p style="text-align:center;color:#999;padding:30px;">Cadastre participantes primeiro. <a href="?_route=participantes">Ir</a></p>
    <?php elseif(empty($abertos)):?>
    <p style="text-align:center;color:#999;padding:30px;">Nenhum jogo aberto. <a href="?_route=jogos">Cadastrar jogos</a></p>
    <?php else:?>
    <div style="background:#fff;border:1px solid #e0e0e0;border-radius:8px;overflow:hidden;">
        <div style="background:#f5f5f5;padding:10px 15px;font-weight:700;font-size:13px;border-bottom:1px solid #e0e0e0;">REGISTRAR PALPITE</div>
        <div style="padding:15px;">
            <form method="post" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
                <input type="hidden" name="_tipoAcao" value="palpitar">
                <div><label style="font-size:11px;font-weight:600;color:#666;display:block;">Participante:</label><select name="participante_id" required style="height:36px;padding:0 10px;border:1px solid #ddd;border-radius:6px;"><?php foreach($participantes as $p):?><option value="<?php echo $p['id'];?>"><?php echo htmlspecialchars($p['nome']);?></option><?php endforeach;?></select></div>
                <div><label style="font-size:11px;font-weight:600;color:#666;display:block;">Jogo:</label><select name="jogo_id" required style="height:36px;padding:0 10px;border:1px solid #ddd;border-radius:6px;"><?php foreach($abertos as $j):?><option value="<?php echo $j['id'];?>"><?php echo $j['time1'].' x '.$j['time2'].' ('.date('d/m',strtotime($j['data_jogo'])).')';?></option><?php endforeach;?></select></div>
                <div style="display:flex;gap:4px;align-items:center;"><label style="font-size:11px;font-weight:600;color:#666;display:block;">Placar:</label><div style="display:flex;gap:4px;align-items:center;"><input type="number" name="gols1" min="0" value="0" style="width:45px;height:36px;text-align:center;border:1px solid #ddd;border-radius:6px;"><strong>x</strong><input type="number" name="gols2" min="0" value="0" style="width:45px;height:36px;text-align:center;border:1px solid #ddd;border-radius:6px;"></div></div>
                <button type="submit" style="height:36px;padding:0 20px;background:#2e7d32;color:white;border:none;border-radius:6px;font-weight:600;cursor:pointer;">Registrar</button>
            </form>
        </div>
    </div>
    <?php endif;?>
</div>
