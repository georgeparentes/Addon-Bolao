<?php
$_tipoAcao = isset($_POST['_tipoAcao']) ? $_POST['_tipoAcao'] : null;
$msg='';
if($_tipoAcao==='add_participante'){adicionarParticipante($connection,$_POST['nome'],$_POST['telefone']??'');$msg="Adicionado!";}
elseif($_tipoAcao==='importar_clientes'){
    $r=mysqli_query($connection,"SELECT id,nome,celular FROM sis_cliente WHERE LOWER(cli_ativado)='s' ORDER BY nome");$q=0;
    if($r){while($row=mysqli_fetch_assoc($r)){$ck=mysqli_query($connection,"SELECT id FROM bolao_participantes WHERE cliente_id=".intval($row['id']));if(!$ck||mysqli_num_rows($ck)==0){adicionarParticipante($connection,$row['nome'],$row['celular']??'',$row['id']);$q++;}}}
    $msg="$q cliente(s) importado(s)!";
}elseif($_tipoAcao==='excluir_participante'){excluirParticipante($connection,$_POST['id']);$msg="Removido!";}
$participantes=listarParticipantes($connection);
?>
<head><link href="src/css/bolao.css" rel="stylesheet" type="text/css"></head>

<div class="bolao-wrapper">
    <div class="bolao-header">
        <h2>&#128101; PARTICIPANTES</h2>
        <p>Gerenciar participantes do bol&atilde;o</p>
    </div>

    <?php if($msg): ?>
    <div class="bolao-msg bolao-msg-success">&#10004; <?php echo $msg; ?></div>
    <?php endif; ?>

    <div class="bolao-nav">
        <a href="?">&#127942; Ranking</a>
        <a href="?_route=jogos">&#9917; Jogos</a>
        <a href="?_route=palpites">&#128221; Palpites</a>
        <a href="?_route=participantes" class="active">&#128101; Participantes</a>
        <a href="?_route=config">&#9881; Config</a>
    </div>

    <div class="bolao-grid">
        <!-- Adicionar -->
        <div class="col-side">
            <div class="bolao-card">
                <div class="bolao-card-header">&#10133; ADICIONAR</div>
                <div class="bolao-card-body">
                    <form method="post" class="bolao-form-stack">
                        <input type="hidden" name="_tipoAcao" value="add_participante">
                        <div>
                            <label class="bolao-label">Nome</label>
                            <input type="text" name="nome" placeholder="Nome do participante" required class="bolao-input" style="width:100%;">
                        </div>
                        <div>
                            <label class="bolao-label">Telefone</label>
                            <input type="text" name="telefone" placeholder="(opcional)" class="bolao-input" style="width:100%;">
                        </div>
                        <button type="submit" class="bolao-btn bolao-btn-primary bolao-btn-full">&#10004; Adicionar</button>
                    </form>
                    <hr style="margin:18px 0;border:none;border-top:1px solid #f1f5f9;">
                    <form method="post">
                        <input type="hidden" name="_tipoAcao" value="importar_clientes">
                        <button type="submit" class="bolao-btn bolao-btn-info bolao-btn-full">&#128229; Importar Clientes MK-AUTH</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Lista -->
        <div class="col-main">
            <div class="bolao-card">
                <div class="bolao-card-header">&#128203; LISTA (<?php echo count($participantes); ?>)</div>
                <div class="bolao-card-body" style="padding:10px 18px;">
                    <div style="margin-bottom:12px;">
                        <input type="text" id="buscarParticipante" placeholder="&#128269; Buscar participante..." class="bolao-input" style="width:100%;">
                    </div>
                    <div style="overflow-x:auto;">
                    <table class="bolao-table">
                        <thead>
                            <tr>
                                <th style="width:40px;">#</th>
                                <th>NOME</th>
                                <th>PALPITES</th>
                                <th style="text-align:center;width:60px;">PTS</th>
                                <th style="text-align:center;width:40px;">X</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if(empty($participantes)): ?>
                        <tr><td colspan="5" class="bolao-empty">Nenhum participante cadastrado</td></tr>
                        <?php else: $i=0; foreach($participantes as $p): $i++;
                            // Verificar se o participante tem fatura em aberto (inadimplente)
                            $inadimplente = false;
                            if ($p['cliente_id']) {
                                $rFat = mysqli_query($connection, "SELECT COUNT(*) as qtd FROM sis_titulo WHERE login = (SELECT login FROM sis_cliente WHERE id = ".intval($p['cliente_id']).") AND status = 'aberto' AND datavenc < CURDATE()");
                                if ($rFat && $fRow = mysqli_fetch_assoc($rFat)) {
                                    $inadimplente = intval($fRow['qtd']) > 0;
                                }
                            }
                            $rPalp = mysqli_query($connection, "SELECT bp.palpite_gols1, bp.palpite_gols2, bp.pontos, bj.time1, bj.time2, bj.gols1, bj.gols2, bj.finalizado FROM bolao_palpites bp INNER JOIN bolao_jogos bj ON bp.jogo_id = bj.id WHERE bp.participante_id = ".intval($p['id'])." ORDER BY bj.data_jogo ASC");
                            $palpites = [];
                            if ($rPalp) { while ($rw = mysqli_fetch_assoc($rPalp)) $palpites[] = $rw; }
                        ?>
                        <tr>
                            <td style="padding:10px 12px;border-bottom:1px solid #f1f5f9;font-weight:600;color:#718096;"><?php echo $i; ?></td>
                            <td style="padding:10px 12px;border-bottom:1px solid #f1f5f9;"><strong><?php echo htmlspecialchars($p['nome']); ?></strong><?php if($inadimplente): ?> <span style="background:#fee2e2;color:#dc2626;font-size:10px;padding:2px 6px;border-radius:4px;font-weight:600;">INADIMPLENTE</span><?php endif; ?></td>
                            <td style="padding:10px 12px;border-bottom:1px solid #f1f5f9;">
                                <?php if(empty($palpites)): ?>
                                    <span style="color:#a0aec0;font-size:11px;">Nenhum palpite</span>
                                <?php else: foreach($palpites as $palp):
                                    $cls = 'palpite-pendente';
                                    if ($palp['finalizado']) {
                                        if ($palp['pontos']>=10) $cls='palpite-acerto';
                                        elseif ($palp['pontos']>=5) $cls='palpite-parcial';
                                        else $cls='palpite-erro';
                                    }
                                ?>
                                    <span class="palpite-badge <?php echo $cls; ?>">
                                        <?php echo $palp['time1'].' '.$palp['palpite_gols1'].'x'.$palp['palpite_gols2'].' '.$palp['time2']; ?>
                                        <?php if($palp['finalizado']): ?> <small>(+<?php echo $palp['pontos']; ?>)</small><?php endif; ?>
                                    </span>
                                <?php endforeach; endif; ?>
                            </td>
                            <td style="padding:10px 12px;border-bottom:1px solid #f1f5f9;"><span class="rank-pts" style="font-size:16px;"><?php echo $p['pontos_total']; ?></span></td>
                            <td style="padding:10px 12px;border-bottom:1px solid #f1f5f9;text-align:center;">
                                <form method="post" style="display:inline">
                                    <input type="hidden" name="_tipoAcao" value="excluir_participante">
                                    <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                    <button type="submit" onclick="return confirm('Remover este participante?')" class="bolao-btn-icon">&#128465;</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $("#buscarParticipante").on("keyup", function(){
        var val = $(this).val().toLowerCase();
        $(".bolao-table tbody tr").filter(function(){
            $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
        });
    });
});
</script>
