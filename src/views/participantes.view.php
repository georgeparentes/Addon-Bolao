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
<h2 align="center" style="padding:12px 0 5px;font-weight:700;">&#128101; PARTICIPANTES</h2>
<?php if($msg):?><div style="background:#e8f5e9;border:1px solid #a5d6a7;color:#2e7d32;padding:10px;border-radius:6px;margin:10px 15px;text-align:center;font-size:13px;"><?php echo $msg;?></div><?php endif;?>
<div style="padding:0 15px;">
    <div style="display:flex;gap:6px;margin-bottom:15px;flex-wrap:wrap;">
        <a href="?" style="padding:8px 14px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:#555;font-size:13px;background:#f8f8f8;">Ranking</a>
        <a href="?_route=jogos" style="padding:8px 14px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:#555;font-size:13px;background:#f8f8f8;">Jogos</a>
        <a href="?_route=palpites" style="padding:8px 14px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:#555;font-size:13px;background:#f8f8f8;">Palpites</a>
        <a href="?_route=participantes" style="padding:8px 14px;border:1px solid #2e7d32;border-radius:6px;text-decoration:none;color:white;background:#2e7d32;font-size:13px;">Participantes</a>
        <a href="?_route=config" style="padding:8px 14px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:#555;font-size:13px;background:#f8f8f8;">Config</a>
    </div>

    <div style="display:flex;gap:15px;flex-wrap:wrap;">
        <!-- Adicionar -->
        <div style="flex:1;min-width:250px;background:#fff;border:1px solid #e0e0e0;border-radius:8px;overflow:hidden;">
            <div style="background:#f5f5f5;padding:10px 15px;font-weight:700;font-size:13px;border-bottom:1px solid #e0e0e0;">ADICIONAR</div>
            <div style="padding:15px;">
                <form method="post" style="display:flex;flex-direction:column;gap:10px;">
                    <input type="hidden" name="_tipoAcao" value="add_participante">
                    <input type="text" name="nome" placeholder="Nome" required style="height:36px;padding:0 10px;border:1px solid #ddd;border-radius:6px;">
                    <input type="text" name="telefone" placeholder="Telefone (opcional)" style="height:36px;padding:0 10px;border:1px solid #ddd;border-radius:6px;">
                    <button type="submit" style="height:36px;background:#2e7d32;color:white;border:none;border-radius:6px;cursor:pointer;font-weight:600;">Adicionar</button>
                </form>
                <hr style="margin:15px 0;border:none;border-top:1px solid #eee;">
                <form method="post"><input type="hidden" name="_tipoAcao" value="importar_clientes"><button type="submit" style="width:100%;height:36px;background:#1976d2;color:white;border:none;border-radius:6px;cursor:pointer;">Importar Clientes MK-AUTH</button></form>
            </div>
        </div>
        <!-- Lista -->
        <div style="flex:2;min-width:300px;background:#fff;border:1px solid #e0e0e0;border-radius:8px;overflow:hidden;">
            <div style="background:#f5f5f5;padding:10px 15px;font-weight:700;font-size:13px;border-bottom:1px solid #e0e0e0;">LISTA (<?php echo count($participantes);?>)</div>
            <div style="padding:10px;overflow-x:auto;">
                <div style="margin-bottom:10px;">
                    <input type="text" id="buscarParticipante" placeholder="Buscar participante..." style="width:100%;height:36px;padding:0 12px;border:1px solid #ddd;border-radius:6px;font-size:13px;">
                </div>
                <table style="width:100%;border-collapse:collapse;font-size:12px;">
                    <thead><tr style="background:#f8f8f8;"><th style="padding:8px;border-bottom:2px solid #e0e0e0;">#</th><th style="padding:8px;text-align:left;border-bottom:2px solid #e0e0e0;">NOME</th><th style="padding:8px;text-align:left;border-bottom:2px solid #e0e0e0;">PALPITES</th><th style="padding:8px;border-bottom:2px solid #e0e0e0;">PTS</th><th style="padding:8px;border-bottom:2px solid #e0e0e0;">X</th></tr></thead>
                    <tbody>
                    <?php if(empty($participantes)):?><tr><td colspan="5" style="text-align:center;padding:20px;color:#999;">Nenhum</td></tr>
                    <?php else:$i=0;foreach($participantes as $p):$i++;
                        // Buscar palpites deste participante
                        $rPalp = mysqli_query($connection, "SELECT bp.palpite_gols1, bp.palpite_gols2, bp.pontos, bj.time1, bj.time2, bj.gols1, bj.gols2, bj.finalizado FROM bolao_palpites bp INNER JOIN bolao_jogos bj ON bp.jogo_id = bj.id WHERE bp.participante_id = ".intval($p['id'])." ORDER BY bj.data_jogo ASC");
                        $palpites = [];
                        if ($rPalp) { while ($rw = mysqli_fetch_assoc($rPalp)) $palpites[] = $rw; }
                    ?>
                    <tr>
                        <td style="padding:6px 8px;border-bottom:1px solid #f0f0f0;"><?php echo $i;?></td>
                        <td style="padding:6px 8px;border-bottom:1px solid #f0f0f0;"><strong><?php echo htmlspecialchars($p['nome']);?></strong></td>
                        <td style="padding:6px 8px;border-bottom:1px solid #f0f0f0;">
                            <?php if(empty($palpites)):?>
                                <span style="color:#999;font-size:11px;">Nenhum palpite</span>
                            <?php else: foreach($palpites as $palp): ?>
                                <div style="display:inline-block;margin:2px 4px 2px 0;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:600;<?php echo $palp['finalizado'] ? ($palp['pontos']>=10?'background:#c8e6c9;color:#2e7d32;':($palp['pontos']>=5?'background:#fff9c4;color:#f9a825;':'background:#ffebee;color:#c62828;')) : 'background:#e3f2fd;color:#1565c0;'; ?>">
                                    <?php echo $palp['time1'].' '.$palp['palpite_gols1'].'x'.$palp['palpite_gols2'].' '.$palp['time2']; ?>
                                    <?php if($palp['finalizado']): ?> <small>(+<?php echo $palp['pontos'];?>)</small><?php endif;?>
                                </div>
                            <?php endforeach; endif;?>
                        </td>
                        <td style="padding:6px 8px;border-bottom:1px solid #f0f0f0;text-align:center;font-weight:700;font-size:14px;color:#2e7d32;"><?php echo $p['pontos_total'];?></td>
                        <td style="padding:6px 8px;border-bottom:1px solid #f0f0f0;"><form method="post" style="display:inline"><input type="hidden" name="_tipoAcao" value="excluir_participante"><input type="hidden" name="id" value="<?php echo $p['id'];?>"><button type="submit" onclick="return confirm('Remover?')" style="background:none;border:none;cursor:pointer;">&#128465;</button></form></td>
                    </tr>
                    <?php endforeach;endif;?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $("#buscarParticipante").on("keyup", function(){
        var val = $(this).val().toLowerCase();
        $("table tbody tr").filter(function(){
            $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
        });
    });
});
</script>