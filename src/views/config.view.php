<?php
$_tipoAcao = isset($_POST['_tipoAcao']) ? $_POST['_tipoAcao'] : null;
$msg='';
if($_tipoAcao==='resetar'){mysqli_query($connection,"TRUNCATE TABLE bolao_palpites");mysqli_query($connection,"UPDATE bolao_participantes SET pontos_total=0");mysqli_query($connection,"UPDATE bolao_jogos SET gols1=NULL,gols2=NULL,finalizado=0");$msg="Resetado!";}
elseif($_tipoAcao==='limpar'){mysqli_query($connection,"TRUNCATE TABLE bolao_palpites");mysqli_query($connection,"TRUNCATE TABLE bolao_participantes");mysqli_query($connection,"TRUNCATE TABLE bolao_jogos");$msg="Tudo apagado!";}
elseif($_tipoAcao==='importar_jogos'){$q=importarJogosCopa2026($connection);$msg=$q>0?"$q jogos importados!":"Ja importados.";}
?>
<head><link href="src/css/bolao.css" rel="stylesheet" type="text/css"></head>
<h2 align="center" style="padding:12px 0 5px;font-weight:700;">&#9881; CONFIGURACOES</h2>
<?php if($msg):?><div style="background:#e8f5e9;border:1px solid #a5d6a7;color:#2e7d32;padding:10px;border-radius:6px;margin:10px 15px;text-align:center;font-size:13px;"><?php echo $msg;?></div><?php endif;?>
<div style="padding:0 15px;">
    <div style="display:flex;gap:6px;margin-bottom:15px;flex-wrap:wrap;">
        <a href="?" style="padding:8px 14px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:#555;font-size:13px;background:#f8f8f8;">Ranking</a>
        <a href="?_route=jogos" style="padding:8px 14px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:#555;font-size:13px;background:#f8f8f8;">Jogos</a>
        <a href="?_route=palpites" style="padding:8px 14px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:#555;font-size:13px;background:#f8f8f8;">Palpites</a>
        <a href="?_route=participantes" style="padding:8px 14px;border:1px solid #ddd;border-radius:6px;text-decoration:none;color:#555;font-size:13px;background:#f8f8f8;">Participantes</a>
        <a href="?_route=config" style="padding:8px 14px;border:1px solid #2e7d32;border-radius:6px;text-decoration:none;color:white;background:#2e7d32;font-size:13px;">Config</a>
    </div>

    <div style="background:#fff;border:1px solid #e0e0e0;border-radius:8px;overflow:hidden;max-width:500px;">
        <div style="background:#f5f5f5;padding:10px 15px;font-weight:700;font-size:13px;border-bottom:1px solid #e0e0e0;">ACOES</div>
        <div style="padding:15px;display:flex;flex-direction:column;gap:10px;">
            <form method="post"><input type="hidden" name="_tipoAcao" value="importar_jogos"><button type="submit" style="width:100%;height:40px;background:#2e7d32;color:white;border:none;border-radius:6px;font-size:13px;cursor:pointer;font-weight:600;">&#9917; Importar Jogos Copa 2026</button></form>
            <form method="post"><input type="hidden" name="_tipoAcao" value="resetar"><button type="submit" onclick="return confirm('Resetar palpites e resultados?')" style="width:100%;height:40px;background:#ff9800;color:white;border:none;border-radius:6px;font-size:13px;cursor:pointer;font-weight:600;">&#128260; Resetar Palpites/Resultados</button></form>
            <form method="post"><input type="hidden" name="_tipoAcao" value="limpar"><button type="submit" onclick="return confirm('APAGAR TUDO?')" style="width:100%;height:40px;background:#f44336;color:white;border:none;border-radius:6px;font-size:13px;cursor:pointer;font-weight:600;">&#128465; Apagar Tudo</button></form>
        </div>
    </div>

    <div style="margin-top:15px;background:#fff;border:1px solid #e0e0e0;border-radius:8px;overflow:hidden;max-width:500px;">
        <div style="background:#f5f5f5;padding:10px 15px;font-weight:700;font-size:13px;border-bottom:1px solid #e0e0e0;">REGRAS</div>
        <div style="padding:15px;font-size:13px;line-height:2;">
            <strong>10 pts</strong> - Placar exato<br>
            <strong>7 pts</strong> - Vencedor + saldo de gols<br>
            <strong>5 pts</strong> - Acertou vencedor/empate<br>
            <strong>2 pts</strong> - Acertou gols de 1 time<br>
            <strong>0 pts</strong> - Errou tudo
        </div>
    </div>
</div>
