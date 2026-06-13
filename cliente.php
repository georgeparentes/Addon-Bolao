<?php
session_start();
include(__DIR__ . '/src/config/database.php');
include(__DIR__ . '/src/models/bolao.model.php');
criarTabelas($connection);

$logado = isset($_SESSION['bolao_cliente_id']);
$msg = ''; $msgErro = '';

if (isset($_POST['_acao'])) {
    $acao = $_POST['_acao'];
    if ($acao === 'login') {
        $cpf = preg_replace('/\D/', '', trim($_POST['cpf'] ?? ''));
        if ($cpf !== '') {
            $cpfEsc = mysqli_real_escape_string($connection, $cpf);
            $r = mysqli_query($connection, "SELECT id, nome FROM sis_cliente WHERE REPLACE(REPLACE(REPLACE(cpf_cnpj,'.',''),'-',''),'/','') = '$cpfEsc' AND LOWER(cli_ativado) = 's' LIMIT 1");
            if ($r && $row = mysqli_fetch_assoc($r)) {
                $_SESSION['bolao_cliente_id'] = $row['id'];
                $_SESSION['bolao_cliente_nome'] = $row['nome'];
                $check = mysqli_query($connection, "SELECT id FROM bolao_participantes WHERE cliente_id = ".intval($row['id']));
                if (!$check || mysqli_num_rows($check) == 0) adicionarParticipante($connection, $row['nome'], '', $row['id']);
                $logado = true;
            } else { $msgErro = 'CPF nao encontrado.'; }
        } else { $msgErro = 'Informe seu CPF.'; }
    } elseif ($acao === 'logout') { unset($_SESSION['bolao_cliente_id']); unset($_SESSION['bolao_cliente_nome']); $logado = false; }
    elseif ($acao === 'palpitar' && $logado) {
        $rP = mysqli_query($connection, "SELECT id FROM bolao_participantes WHERE cliente_id = ".intval($_SESSION['bolao_cliente_id']));
        if ($rP && $pRow = mysqli_fetch_assoc($rP)) {
            $resultado = registrarPalpite($connection, intval($_POST['jogo_id']), $pRow['id'], intval($_POST['gols1']), intval($_POST['gols2']));
            if ($resultado) $msg = 'Palpite registrado!';
            else $msgErro = 'Tempo esgotado! Palpites encerram 1h antes do jogo.';
        }
    }
}

$ranking = obterRanking($connection);
$jogos = obterJogos($connection, 'todos');
$abertos = array_values(array_filter($jogos, function($j){return !$j['finalizado'];}));
$finalizados = array_values(array_filter($jogos, function($j){return $j['finalizado'];}));
$meusPalpites = [];
if ($logado) {
    $rP = mysqli_query($connection, "SELECT id FROM bolao_participantes WHERE cliente_id = ".intval($_SESSION['bolao_cliente_id']));
    if ($rP && $pRow = mysqli_fetch_assoc($rP)) {
        $rPalp = mysqli_query($connection, "SELECT * FROM bolao_palpites WHERE participante_id = ".$pRow['id']);
        if ($rPalp) while ($palp = mysqli_fetch_assoc($rPalp)) $meusPalpites[$palp['jogo_id']] = $palp;
    }
}
?><!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Bolao Copa 2026</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Segoe UI',sans-serif;background:#f0f2f5;min-height:100vh}
.hd{background:linear-gradient(135deg,#1b5e20,#2e7d32,#43a047);color:white;padding:20px;text-align:center}
.hd h1{font-size:22px;margin-bottom:4px}.hd p{font-size:12px;opacity:.9}
.ct{max-width:700px;margin:0 auto;padding:15px}
.login-box{background:white;border-radius:12px;padding:30px;max-width:380px;margin:30px auto;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,.1)}
.login-box h2{margin-bottom:20px;color:#333;font-size:18px}
.login-box input{width:100%;padding:12px;margin-bottom:12px;border:2px solid #e0e0e0;border-radius:8px;font-size:14px;outline:none}
.login-box input:focus{border-color:#2e7d32}
.login-box button{width:100%;padding:12px;background:#2e7d32;color:white;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer}
.login-box button:hover{background:#1b5e20}
.tabs{display:flex;gap:4px;margin-bottom:15px;flex-wrap:wrap}
.tab{padding:9px 16px;background:white;border:1px solid #ddd;border-radius:8px;cursor:pointer;font-size:13px;font-weight:500;text-decoration:none;color:#555}
.tab:hover{background:#e8f5e9;border-color:#4caf50}.tab.active{background:#2e7d32;color:white;border-color:#2e7d32}
.card{background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08);margin-bottom:14px}
.card-h{background:#f8f8f8;padding:12px 16px;font-weight:700;font-size:13px;border-bottom:1px solid #eee}
.card-b{padding:14px}
.jogo{display:flex;align-items:center;justify-content:space-between;padding:10px;border:1px solid #f0f0f0;border-radius:8px;margin-bottom:6px}
.jogo:hover{background:#f9f9f9}.jogo-t{flex:1;text-align:center;font-weight:600;font-size:13px}
.jogo-d{font-size:11px;color:#888;min-width:55px}.jogo-p{min-width:90px;text-align:center}
.pi{display:flex;align-items:center;gap:3px}.pi input{width:36px;height:30px;text-align:center;border:2px solid #ddd;border-radius:6px;font-size:13px;font-weight:700}
.pi input:focus{border-color:#2e7d32;outline:none}.pi button{height:30px;padding:0 8px;background:#2e7d32;color:white;border:none;border-radius:6px;cursor:pointer;font-size:11px}
.pf{background:#e8f5e9;padding:3px 8px;border-radius:4px;font-weight:700;color:#2e7d32;font-size:12px}
.ri{display:flex;align-items:center;padding:10px 12px;border-bottom:1px solid #f5f5f5;gap:10px}
.ri:last-child{border:none}.ri-p{font-size:16px;min-width:32px;text-align:center}.ri-n{flex:1;font-weight:500;font-size:13px}.ri-pts{font-size:16px;font-weight:800;color:#2e7d32}
.ub{display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:white;border-radius:8px;margin-bottom:14px;box-shadow:0 1px 4px rgba(0,0,0,.05)}
.ub button{background:#eee;border:none;padding:6px 12px;border-radius:4px;font-size:12px;cursor:pointer}
.msg-ok{background:#e8f5e9;color:#2e7d32;padding:10px;border-radius:6px;margin-bottom:12px;text-align:center;font-size:13px}
.msg-err{background:#ffebee;color:#c62828;padding:10px;border-radius:6px;margin-bottom:12px;text-align:center;font-size:13px}
.plf{background:#2e7d32;color:white;padding:2px 8px;border-radius:4px;font-weight:700;font-size:12px}
@media(max-width:600px){.jogo{flex-direction:column;gap:6px;text-align:center}.jogo-d,.jogo-p{min-width:auto}}
</style></head><body>
<div class="hd"><h1>&#9917; Bolao Copa do Mundo 2026</h1><p>Faca seus palpites e concorra!</p></div>
<div class="ct">
<?php if(!$logado):?>
<div class="login-box">
    <h2>&#128274; Entrar no Bolao</h2>
    <?php if($msgErro):?><div class="msg-err"><?php echo $msgErro;?></div><?php endif;?>
    <form method="post"><input type="hidden" name="_acao" value="login"><input type="text" name="cpf" placeholder="Digite seu CPF" required maxlength="14" oninput="this.value=this.value.replace(/\D/g,'').replace(/(\d{3})(\d)/,'$1.$2').replace(/(\d{3})(\d)/,'$1.$2').replace(/(\d{3})(\d{1,2})$/,'$1-$2')"><button type="submit">Entrar</button></form>
    <p style="margin-top:12px;font-size:12px;color:#888;">Use o CPF cadastrado no provedor</p>
</div>
<?php else:?>
<?php if($msg):?><div class="msg-ok"><?php echo $msg;?></div><?php endif;?>
<div class="ub"><span>&#128075; <strong><?php echo htmlspecialchars($_SESSION['bolao_cliente_nome']);?></strong></span><form method="post" style="display:inline"><input type="hidden" name="_acao" value="logout"><button type="submit">Sair</button></form></div>

<?php $tab=isset($_GET['tab'])?$_GET['tab']:'palpites';?>
<div class="tabs">
    <a href="?tab=palpites" class="tab <?php echo $tab==='palpites'?'active':'';?>">Palpites</a>
    <a href="?tab=ranking" class="tab <?php echo $tab==='ranking'?'active':'';?>">Ranking</a>
    <a href="?tab=resultados" class="tab <?php echo $tab==='resultados'?'active':'';?>">Resultados</a>
</div>

<?php if($tab==='palpites'):?>
<div class="card"><div class="card-h">&#128221; Seus Palpites</div><div class="card-b">
<?php if(empty($abertos)):?><p style="text-align:center;color:#999;padding:15px;">Nenhum jogo aberto.</p>
<?php else: foreach($abertos as $j): $ja=isset($meusPalpites[$j['id']]);
    $limite = strtotime($j['data_jogo']) - 3600;
    $tempoRestante = $limite - time();
    $expirou = ($tempoRestante <= 0);
?>
<div class="jogo">
    <div class="jogo-d"><?php echo date('d/m',strtotime($j['data_jogo']));?><br><small><?php echo date('H:i',strtotime($j['data_jogo']));?></small>
        <?php if(!$ja && !$expirou && $tempoRestante < 7200):?><br><small style="color:#f44336;">Fecha em <?php echo floor($tempoRestante/3600).'h'.str_pad(floor(($tempoRestante%3600)/60),2,'0',STR_PAD_LEFT);?></small><?php endif;?>
    </div>
    <div class="jogo-t"><?php echo $j['time1'];?> <span style="color:#999">x</span> <?php echo $j['time2'];?></div>
    <div class="jogo-p">
        <?php if($ja):?><span class="pf"><?php echo $meusPalpites[$j['id']]['palpite_gols1'].'x'.$meusPalpites[$j['id']]['palpite_gols2'];?></span>
        <?php elseif($expirou):?><span style="font-size:11px;color:#f44336;">Encerrado</span>
        <?php else:?><form method="post" class="pi"><input type="hidden" name="_acao" value="palpitar"><input type="hidden" name="jogo_id" value="<?php echo $j['id'];?>"><input type="number" name="gols1" min="0" value="0"><span style="font-weight:700">x</span><input type="number" name="gols2" min="0" value="0"><button type="submit">OK</button></form><?php endif;?>
    </div>
</div>
<?php endforeach;endif;?>
</div></div>

<?php elseif($tab==='ranking'):?>
<div class="card"><div class="card-h">&#127942; Ranking</div><div class="card-b" style="padding:0">
<?php if(empty($ranking)):?><p style="text-align:center;color:#999;padding:20px;">Nenhum participante.</p>
<?php else:$pos=0;foreach($ranking as $r):$pos++;$me=isset($_SESSION['bolao_cliente_id'])&&$r['cliente_id']==$_SESSION['bolao_cliente_id'];?>
<div class="ri" style="<?php echo $me?'background:#e8f5e9;':'';?>">
    <div class="ri-p"><?php echo $pos==1?'&#129351;':($pos==2?'&#129352;':($pos==3?'&#129353;':$pos.'o'));?></div>
    <div class="ri-n"><?php echo htmlspecialchars($r['nome']);?></div>
    <div class="ri-pts"><?php echo $r['pontos_total'];?></div>
</div>
<?php endforeach;endif;?>
</div></div>

<?php elseif($tab==='resultados'):?>
<div class="card"><div class="card-h">&#9917; Resultados</div><div class="card-b">
<?php if(empty($finalizados)):?><p style="text-align:center;color:#999;padding:15px;">Nenhum jogo finalizado.</p>
<?php else: foreach($finalizados as $j): $mp=isset($meusPalpites[$j['id']])?$meusPalpites[$j['id']]:null;?>
<div class="jogo">
    <div class="jogo-d"><?php echo date('d/m',strtotime($j['data_jogo']));?></div>
    <div class="jogo-t"><?php echo $j['time1'];?> <span class="plf"><?php echo $j['gols1'].' - '.$j['gols2'];?></span> <?php echo $j['time2'];?></div>
    <div class="jogo-p"><?php if($mp):?><div style="font-size:11px;color:#888;">Voce: <?php echo $mp['palpite_gols1'].'x'.$mp['palpite_gols2'];?></div><div style="font-weight:700;color:#2e7d32;">+<?php echo $mp['pontos'];?>pts</div><?php else:?><span style="font-size:11px;color:#999;">--</span><?php endif;?></div>
</div>
<?php endforeach;endif;?>
</div></div>
<?php endif;endif;?>
</div></body></html>
