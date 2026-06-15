<?php
// VERSAO 10 - CORRIGIDO: tabela sis_lanc (nao sis_titulo)
session_start();
include(__DIR__ . '/src/config/database.php');
include(__DIR__ . '/src/models/bolao.model.php');
criarTabelas($connection);

$logado = isset($_SESSION['bolao_cliente_id']);
$msg = ''; $msgErro = '';

// Verifica se cliente tem titulo vencido no MK-AUTH
// Tabela correta: sis_lanc (lancamentos financeiros)
function clienteEmAtraso($con, $cid) {
    $cid = intval($cid);
    $rc = mysqli_query($con, "SELECT login, nome FROM sis_cliente WHERE id = $cid LIMIT 1");
    if (!$rc || !($c = mysqli_fetch_assoc($rc))) return false;
    $lg = mysqli_real_escape_string($con, trim($c['login'] ?? ''));
    $nm = mysqli_real_escape_string($con, trim($c['nome'] ?? ''));
    
    // Buscar em sis_lanc por login - status vencido
    if ($lg !== '') {
        $q = @mysqli_query($con, "SELECT id FROM sis_lanc WHERE login='$lg' AND LOWER(TRIM(status))='vencido' LIMIT 1");
        if ($q && mysqli_num_rows($q) > 0) return true;
        // Qualquer titulo nao pago com vencimento passado
        $q2 = @mysqli_query($con, "SELECT id FROM sis_lanc WHERE login='$lg' AND LOWER(TRIM(status)) NOT IN ('pago','cancelado','removido') AND datavenc < CURDATE() LIMIT 1");
        if ($q2 && mysqli_num_rows($q2) > 0) return true;
    }
    
    // Buscar em sis_lanc por id_cliente
    $q3 = @mysqli_query($con, "SELECT id FROM sis_lanc WHERE id_cliente=$cid AND LOWER(TRIM(status))='vencido' LIMIT 1");
    if ($q3 && mysqli_num_rows($q3) > 0) return true;
    $q4 = @mysqli_query($con, "SELECT id FROM sis_lanc WHERE id_cliente=$cid AND LOWER(TRIM(status)) NOT IN ('pago','cancelado','removido') AND datavenc < CURDATE() LIMIT 1");
    if ($q4 && mysqli_num_rows($q4) > 0) return true;
    
    // Buscar em sis_boleto tambem (fallback)
    if ($lg !== '') {
        $q5 = @mysqli_query($con, "SELECT id FROM sis_boleto WHERE login='$lg' AND LOWER(TRIM(status))='vencido' LIMIT 1");
        if ($q5 && mysqli_num_rows($q5) > 0) return true;
    }
    $q6 = @mysqli_query($con, "SELECT id FROM sis_boleto WHERE id_cliente=$cid AND LOWER(TRIM(status))='vencido' LIMIT 1");
    if ($q6 && mysqli_num_rows($q6) > 0) return true;
    
    return false;
}

// Verificar sessao ativa - deslogar se em atraso
if ($logado) {
    $checkId = intval($_SESSION['bolao_cliente_id']);
    $podeParticipar = true;
    
    // Verificar bloqueio
    $chk = mysqli_query($connection, "SELECT bloqueado FROM sis_cliente WHERE id=$checkId LIMIT 1");
    if ($chk && $ckr = mysqli_fetch_assoc($chk)) {
        if (strtolower($ckr['bloqueado'] ?? 'nao') === 'sim') $podeParticipar = false;
    }
    // Verificar atraso
    if ($podeParticipar && clienteEmAtraso($connection, $checkId)) {
        $podeParticipar = false;
    }
    
    if (!$podeParticipar) {
        unset($_SESSION['bolao_cliente_id']);
        unset($_SESSION['bolao_cliente_nome']);
        $logado = false;
        $msgErro = 'Voce possui fatura em atraso. Regularize sua situacao para participar do bolao.';
    }
}

// PROCESSAR ACOES
if (isset($_POST['_acao'])) {
    $acao = $_POST['_acao'];
    
    if ($acao === 'login') {
        $cpf = preg_replace('/\D/', '', trim($_POST['cpf'] ?? ''));
        if ($cpf !== '') {
            $cpfEsc = mysqli_real_escape_string($connection, $cpf);
            $r = mysqli_query($connection, "SELECT id, nome, bloqueado FROM sis_cliente WHERE REPLACE(REPLACE(REPLACE(cpf_cnpj,'.',''),'-',''),'/','') = '$cpfEsc' AND LOWER(cli_ativado)='s' LIMIT 1");
            if ($r && $row = mysqli_fetch_assoc($r)) {
                if (strtolower($row['bloqueado'] ?? 'nao') === 'sim') {
                    $msgErro = 'Seu acesso esta bloqueado. Regularize sua situacao para participar do bolao.';
                } elseif (clienteEmAtraso($connection, intval($row['id']))) {
                    $msgErro = 'Voce possui fatura em atraso. Regularize para participar do bolao.';
                } else {
                    $_SESSION['bolao_cliente_id'] = $row['id'];
                    $_SESSION['bolao_cliente_nome'] = $row['nome'];
                    $check = mysqli_query($connection, "SELECT id FROM bolao_participantes WHERE cliente_id=".intval($row['id']));
                    if (!$check || mysqli_num_rows($check) == 0) adicionarParticipante($connection, $row['nome'], '', $row['id']);
                    $logado = true;
                }
            } else { 
                $msgErro = 'CPF nao encontrado ou cliente inativo.'; 
            }
        } else { 
            $msgErro = 'Informe seu CPF.'; 
        }
    } elseif ($acao === 'logout') { 
        unset($_SESSION['bolao_cliente_id']); 
        unset($_SESSION['bolao_cliente_nome']); 
        $logado = false; 
    } elseif ($acao === 'palpitar' && $logado) {
        $cliId = intval($_SESSION['bolao_cliente_id']);
        if (clienteEmAtraso($connection, $cliId)) {
            unset($_SESSION['bolao_cliente_id']);
            unset($_SESSION['bolao_cliente_nome']);
            $logado = false;
            $msgErro = 'Voce possui fatura em atraso. Regularize sua situacao para participar do bolao.';
        } else {
            $rP = mysqli_query($connection, "SELECT id FROM bolao_participantes WHERE cliente_id=$cliId");
            if ($rP && $pRow = mysqli_fetch_assoc($rP)) {
                $resultado = registrarPalpite($connection, intval($_POST['jogo_id']), $pRow['id'], intval($_POST['gols1']), intval($_POST['gols2']));
                if ($resultado) $msg = 'Palpite registrado com sucesso!';
                else $msgErro = 'Nao foi possivel registrar. Jogo ja iniciou ou encerrado.';
            } else {
                $msgErro = 'Participante nao encontrado. Faca logout e entre novamente.';
            }
        }
    }
}

$ranking = obterRanking($connection);
$jogos = obterJogos($connection, 'todos');
$abertos = array_values(array_filter($jogos, function($j){return !$j['finalizado'];}));
$finalizados = array_values(array_filter($jogos, function($j){return $j['finalizado'];}));
$meusPalpites = [];
if ($logado) {
    $rP = mysqli_query($connection, "SELECT id FROM bolao_participantes WHERE cliente_id=".intval($_SESSION['bolao_cliente_id']));
    if ($rP && $pRow = mysqli_fetch_assoc($rP)) {
        $rPalp = mysqli_query($connection, "SELECT * FROM bolao_palpites WHERE participante_id=".$pRow['id']);
        if ($rPalp) while ($palp = mysqli_fetch_assoc($rPalp)) $meusPalpites[$palp['jogo_id']] = $palp;
    }
}
?><!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Bolao Copa 2026 - AGF NET</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Poppins',sans-serif;background:linear-gradient(135deg,#0f1923 0%,#1a2a3a 100%);min-height:100vh;color:#fff}
.header{background:linear-gradient(135deg,#1b5e20 0%,#2e7d32 50%,#43a047 100%);padding:24px 20px;text-align:center;position:relative;overflow:hidden}
.header::before{content:'';position:absolute;top:-50%;left:-50%;width:200%;height:200%;background:radial-gradient(circle,rgba(255,255,255,0.05) 0%,transparent 70%);animation:pulse 4s ease-in-out infinite}
@keyframes pulse{0%,100%{transform:scale(1)}50%{transform:scale(1.05)}}
.header h1{font-size:22px;font-weight:800;letter-spacing:-0.5px;position:relative}
.header p{font-size:12px;opacity:0.85;margin-top:4px;position:relative}
.ct{max-width:600px;margin:0 auto;padding:16px}
.login-card{background:rgba(255,255,255,0.05);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.1);border-radius:16px;padding:32px;max-width:380px;margin:30px auto;text-align:center}
.login-card h2{font-size:20px;font-weight:700;margin-bottom:20px;color:#fff}
.login-card input{width:100%;padding:14px 16px;margin-bottom:14px;border:1px solid rgba(255,255,255,0.15);border-radius:10px;font-size:15px;outline:none;background:rgba(255,255,255,0.08);color:#fff;transition:border 0.3s}
.login-card input::placeholder{color:rgba(255,255,255,0.4)}
.login-card input:focus{border-color:#4caf50;background:rgba(255,255,255,0.12)}
.login-card button{width:100%;padding:14px;background:linear-gradient(135deg,#2e7d32,#43a047);color:white;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;transition:transform 0.2s,box-shadow 0.2s}
.login-card button:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(46,125,50,0.4)}
.login-card .hint{margin-top:14px;font-size:12px;color:rgba(255,255,255,0.4)}
.user-bar{display:flex;justify-content:space-between;align-items:center;padding:12px 16px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:12px;margin-bottom:16px}
.user-bar .nome{font-weight:600;font-size:14px}
.user-bar button{background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);padding:6px 14px;border-radius:6px;font-size:12px;cursor:pointer;color:#fff;transition:background 0.2s}
.user-bar button:hover{background:rgba(255,255,255,0.2)}
.tabs{display:flex;gap:4px;margin-bottom:18px;background:rgba(255,255,255,0.05);border-radius:12px;padding:4px;border:1px solid rgba(255,255,255,0.08)}
.tab{flex:1;padding:10px 8px;text-align:center;border-radius:10px;font-size:12px;font-weight:600;text-decoration:none;color:rgba(255,255,255,0.6);transition:all 0.2s}
.tab:hover{color:#fff;background:rgba(255,255,255,0.08)}
.tab.active{background:linear-gradient(135deg,#2e7d32,#43a047);color:white;box-shadow:0 4px 15px rgba(46,125,50,0.3)}
.card{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);border-radius:14px;overflow:hidden;margin-bottom:16px}
.card-h{padding:14px 18px;font-weight:700;font-size:14px;border-bottom:1px solid rgba(255,255,255,0.08);background:rgba(255,255,255,0.03)}
.card-b{padding:16px}
.jogo{display:flex;align-items:center;justify-content:space-between;padding:14px;border:1px solid rgba(255,255,255,0.06);border-radius:12px;margin-bottom:8px;background:rgba(255,255,255,0.02);transition:background 0.2s}
.jogo:hover{background:rgba(255,255,255,0.06)}
.jogo-d{font-size:11px;color:rgba(255,255,255,0.5);min-width:55px;text-align:center}
.jogo-d small{color:#4caf50}
.jogo-t{flex:1;text-align:center;font-weight:600;font-size:14px}
.jogo-t .vs{color:rgba(255,255,255,0.3);margin:0 6px;font-weight:400;font-size:12px}
.jogo-p{min-width:100px;text-align:center}
.pi{display:flex;align-items:center;gap:4px}
.pi input[type="number"]{width:38px;height:34px;text-align:center;border:1px solid rgba(255,255,255,0.2);border-radius:8px;font-size:15px;font-weight:700;background:rgba(255,255,255,0.08);color:#fff;outline:none}
.pi input:focus{border-color:#4caf50}
.pi span{font-weight:700;color:rgba(255,255,255,0.4)}
.pi button{height:34px;padding:0 12px;background:linear-gradient(135deg,#2e7d32,#43a047);color:white;border:none;border-radius:8px;cursor:pointer;font-size:12px;font-weight:600;transition:transform 0.2s}
.pi button:hover{transform:scale(1.05)}
.pf{background:linear-gradient(135deg,#1b5e20,#2e7d32);padding:6px 12px;border-radius:8px;font-weight:700;color:white;font-size:13px;display:inline-block}
.encerrado{font-size:11px;color:#ef5350;font-weight:500}
.ri{display:flex;align-items:center;padding:12px 16px;border-bottom:1px solid rgba(255,255,255,0.05);gap:14px;transition:background 0.2s}
.ri:last-child{border:none}
.ri:hover{background:rgba(255,255,255,0.03)}
.ri-p{font-size:20px;min-width:36px;text-align:center}
.ri-n{flex:1;font-weight:500;font-size:14px}
.ri-pts{font-size:20px;font-weight:800;color:#4caf50}
.ri.me{background:rgba(76,175,80,0.1);border-radius:10px;border:1px solid rgba(76,175,80,0.2)}
.plf{background:linear-gradient(135deg,#2e7d32,#43a047);color:white;padding:4px 10px;border-radius:6px;font-weight:700;font-size:12px;display:inline-block}
.pts-ganho{font-weight:800;color:#4caf50;font-size:13px}
.pts-zero{color:rgba(255,255,255,0.3)}
.msg-ok{background:rgba(76,175,80,0.15);border:1px solid rgba(76,175,80,0.3);color:#66bb6a;padding:12px;border-radius:10px;margin-bottom:14px;text-align:center;font-size:13px;font-weight:500}
.msg-err{background:rgba(239,83,80,0.15);border:1px solid rgba(239,83,80,0.3);color:#ef5350;padding:12px;border-radius:10px;margin-bottom:14px;text-align:center;font-size:13px;font-weight:500}
.versao{text-align:center;font-size:9px;color:rgba(255,255,255,0.2);margin-top:20px}
@media(max-width:500px){.jogo{flex-direction:column;gap:8px;text-align:center}.jogo-d,.jogo-p{min-width:auto}.tabs{flex-wrap:wrap}.tab{font-size:11px;padding:8px 6px}}
</style></head><body>

<div class="header">
    <img src="assets/logo.png" alt="AGF NET" style="height:130px;margin-bottom:12px;object-fit:contain;max-width:90%;">
    <h1>Bolao Copa do Mundo 2026</h1>
    <p>EUA &bull; Mexico &bull; Canada</p>
</div>

<div class="ct">

<?php if(!$logado):?>
<div class="login-card">
    <h2>Entrar no Bolao</h2>
    <?php if($msgErro):?><div class="msg-err"><?php echo $msgErro;?></div><?php endif;?>
    <form method="post">
        <input type="hidden" name="_acao" value="login">
        <input type="text" name="cpf" placeholder="Digite seu CPF" required maxlength="14" oninput="this.value=this.value.replace(/\D/g,'').replace(/(\d{3})(\d)/,'$1.$2').replace(/(\d{3})(\d)/,'$1.$2').replace(/(\d{3})(\d{1,2})$/,'$1-$2')">
        <button type="submit">Entrar</button>
    </form>
    <p class="hint">Use o CPF cadastrado no provedor</p>
</div>

<?php else:?>
<?php if($msg):?><div class="msg-ok"><?php echo $msg;?></div><?php endif;?>
<?php if($msgErro):?><div class="msg-err"><?php echo $msgErro;?></div><?php endif;?>

<div class="user-bar">
    <span class="nome">&#128075; <?php echo htmlspecialchars($_SESSION['bolao_cliente_nome']);?></span>
    <form method="post" style="display:inline"><input type="hidden" name="_acao" value="logout"><button type="submit">Sair</button></form>
</div>

<?php $tab=isset($_GET['tab'])?$_GET['tab']:'palpites';?>
<div class="tabs">
    <a href="?tab=palpites" class="tab <?php echo $tab==='palpites'?'active':'';?>">Palpites</a>
    <a href="?tab=anteriores" class="tab <?php echo $tab==='anteriores'?'active':'';?>">Anteriores</a>
    <a href="?tab=ranking" class="tab <?php echo $tab==='ranking'?'active':'';?>">Ranking</a>
    <a href="?tab=resultados" class="tab <?php echo $tab==='resultados'?'active':'';?>">Resultados</a>
</div>

<?php if($tab==='palpites'):?>
<div class="card"><div class="card-h">&#128221; Jogos Abertos</div><div class="card-b">
<?php if(empty($abertos)):?><p style="text-align:center;color:rgba(255,255,255,0.4);padding:20px;">Nenhum jogo aberto no momento.</p>
<?php else: foreach($abertos as $j): $ja=isset($meusPalpites[$j['id']]);
    $limite = strtotime($j['data_jogo']) - 600;
    $tempoRestante = $limite - time();
    $expirou = ($tempoRestante <= 0);
?>
<div class="jogo">
    <div class="jogo-d"><?php echo date('d/m',strtotime($j['data_jogo']));?><br><small><?php echo date('H:i',strtotime($j['data_jogo']));?></small>
        <?php if(!$ja && !$expirou && $tempoRestante < 7200):?><br><small style="color:#ef5350;font-size:9px;">Fecha em <?php echo floor($tempoRestante/3600).'h'.str_pad(floor(($tempoRestante%3600)/60),2,'0',STR_PAD_LEFT);?></small><?php endif;?>
    </div>
    <div class="jogo-t"><?php echo $j['time1'];?><span class="vs">vs</span><?php echo $j['time2'];?></div>
    <div class="jogo-p">
        <?php if($ja):?><span class="pf"><?php echo $meusPalpites[$j['id']]['palpite_gols1'].' x '.$meusPalpites[$j['id']]['palpite_gols2'];?></span>
        <?php elseif($expirou):?><span class="encerrado">Encerrado</span>
        <?php else:?><form method="post" class="pi"><input type="hidden" name="_acao" value="palpitar"><input type="hidden" name="jogo_id" value="<?php echo $j['id'];?>"><input type="number" name="gols1" min="0" value="0"><span>x</span><input type="number" name="gols2" min="0" value="0"><button type="submit">OK</button></form><?php endif;?>
    </div>
</div>
<?php endforeach;endif;?>
</div></div>

<?php elseif($tab==='anteriores'):?>
<div class="card"><div class="card-h">&#128203; Historico de Palpites</div><div class="card-b">
<?php
$meuId = null;
$rP2 = mysqli_query($connection, "SELECT id FROM bolao_participantes WHERE cliente_id=".intval($_SESSION['bolao_cliente_id']));
if ($rP2 && $pRow2 = mysqli_fetch_assoc($rP2)) $meuId = $pRow2['id'];
if ($meuId) {
    $rHist = mysqli_query($connection, "SELECT bp.palpite_gols1, bp.palpite_gols2, bp.pontos, bj.time1, bj.time2, bj.gols1, bj.gols2, bj.data_jogo, bj.finalizado FROM bolao_palpites bp INNER JOIN bolao_jogos bj ON bp.jogo_id=bj.id WHERE bp.participante_id=$meuId ORDER BY bj.data_jogo DESC");
    $historico = []; if ($rHist) while ($rw = mysqli_fetch_assoc($rHist)) $historico[] = $rw;
    if (empty($historico)):?><p style="text-align:center;color:rgba(255,255,255,0.4);padding:20px;">Nenhum palpite ainda.</p>
    <?php else: foreach($historico as $h):
        $ptsCor='rgba(255,255,255,0.3)';
        if($h['finalizado']){if($h['pontos']>=10)$ptsCor='#4caf50';elseif($h['pontos']>=5)$ptsCor='#ffc107';elseif($h['pontos']>=2)$ptsCor='#ff9800';else $ptsCor='#ef5350';}
    ?>
    <div class="jogo">
        <div class="jogo-d"><?php echo date('d/m',strtotime($h['data_jogo']));?></div>
        <div class="jogo-t"><?php echo $h['time1'];?><span class="vs">vs</span><?php echo $h['time2'];?></div>
        <div class="jogo-p" style="display:flex;flex-direction:column;gap:2px;align-items:center;">
            <span style="font-size:11px;color:rgba(255,255,255,0.5);">Palpite: <strong style="color:#fff"><?php echo $h['palpite_gols1'].'x'.$h['palpite_gols2'];?></strong></span>
            <?php if($h['finalizado']):?><span style="font-size:11px;color:rgba(255,255,255,0.5);">Real: <strong class="plf" style="font-size:10px;padding:2px 6px;"><?php echo $h['gols1'].'x'.$h['gols2'];?></strong></span>
            <span style="font-weight:800;color:<?php echo $ptsCor;?>;font-size:14px;">+<?php echo $h['pontos'];?></span>
            <?php else:?><span style="font-size:10px;color:rgba(255,255,255,0.3);">Aguardando</span><?php endif;?>
        </div>
    </div>
    <?php endforeach; endif;
} ?>
</div></div>

<?php elseif($tab==='ranking'):?>
<div class="card"><div class="card-h">&#127942; Ganhadores</div><div class="card-b" style="padding:0">
<?php
$ganhadores = array_filter($ranking, function($r){ return $r['pontos_total'] > 0; });
if(empty($ganhadores)):?><p style="text-align:center;color:rgba(255,255,255,0.4);padding:30px;">Nenhum ganhador ainda. Faca seus palpites!</p>
<?php else:$pos=0;foreach($ganhadores as $r):$pos++;$me=isset($_SESSION['bolao_cliente_id'])&&$r['cliente_id']==$_SESSION['bolao_cliente_id'];?>
<div class="ri <?php echo $me?'me':'';?>">
    <div class="ri-p"><?php echo $pos==1?'&#129351;':($pos==2?'&#129352;':($pos==3?'&#129353;':$pos.'&#186;'));?></div>
    <div class="ri-n"><?php echo htmlspecialchars($r['nome']);?></div>
    <div class="ri-pts"><?php echo $r['pontos_total'];?></div>
</div>
<?php endforeach;endif;?>
</div></div>

<?php elseif($tab==='resultados'):?>
<div class="card"><div class="card-h">&#9917; Jogos Finalizados</div><div class="card-b">
<?php if(empty($finalizados)):?><p style="text-align:center;color:rgba(255,255,255,0.4);padding:20px;">Nenhum jogo finalizado.</p>
<?php else: foreach($finalizados as $j): $mp=isset($meusPalpites[$j['id']])?$meusPalpites[$j['id']]:null;?>
<div class="jogo">
    <div class="jogo-d"><?php echo date('d/m',strtotime($j['data_jogo']));?></div>
    <div class="jogo-t"><?php echo $j['time1'];?> <span class="plf"><?php echo $j['gols1'].' - '.$j['gols2'];?></span> <?php echo $j['time2'];?></div>
    <div class="jogo-p"><?php if($mp):?><div style="font-size:10px;color:rgba(255,255,255,0.5);">Seu: <?php echo $mp['palpite_gols1'].'x'.$mp['palpite_gols2'];?></div><div class="pts-ganho">+<?php echo $mp['pontos'];?></div><?php else:?><span class="pts-zero" style="font-size:11px;">--</span><?php endif;?></div>
</div>
<?php endforeach;endif;?>
</div></div>
<?php endif;endif;?>

<div class="versao">v10</div>
</div></body></html>
