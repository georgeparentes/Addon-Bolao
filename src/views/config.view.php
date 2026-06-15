<?php
$_tipoAcao = isset($_POST['_tipoAcao']) ? $_POST['_tipoAcao'] : null;
$msg='';
if($_tipoAcao==='resetar'){mysqli_query($connection,"TRUNCATE TABLE bolao_palpites");mysqli_query($connection,"UPDATE bolao_participantes SET pontos_total=0");mysqli_query($connection,"UPDATE bolao_jogos SET gols1=NULL,gols2=NULL,finalizado=0");$msg="Resetado!";}
elseif($_tipoAcao==='limpar'){mysqli_query($connection,"TRUNCATE TABLE bolao_palpites");mysqli_query($connection,"TRUNCATE TABLE bolao_participantes");mysqli_query($connection,"TRUNCATE TABLE bolao_jogos");$msg="Tudo apagado!";}
elseif($_tipoAcao==='importar_jogos'){$q=importarJogosCopa2026($connection);$msg=$q>0?"$q jogos importados!":"Ja importados.";}
?>
<head><link href="src/css/bolao.css" rel="stylesheet" type="text/css"></head>

<div class="bolao-wrapper">
    <div class="bolao-header">
        <h2>&#9881; CONFIGURA&Ccedil;&Otilde;ES</h2>
        <p>A&ccedil;&otilde;es e regras do bol&atilde;o</p>
    </div>

    <?php if($msg): ?>
    <div class="bolao-msg bolao-msg-success">&#10004; <?php echo $msg; ?></div>
    <?php endif; ?>

    <div class="bolao-nav">
        <a href="?">&#127942; Ranking</a>
        <a href="?_route=jogos">&#9917; Jogos</a>
        <a href="?_route=palpites">&#128221; Palpites</a>
        <a href="?_route=participantes">&#128101; Participantes</a>
        <a href="?_route=config" class="active">&#9881; Config</a>
    </div>

    <div class="bolao-grid">
        <!-- Acoes -->
        <div class="col-side">
            <div class="bolao-card">
                <div class="bolao-card-header">&#128736; A&Ccedil;&Otilde;ES</div>
                <div class="bolao-card-body">
                    <div class="bolao-actions">
                        <form method="post">
                            <input type="hidden" name="_tipoAcao" value="importar_jogos">
                            <button type="submit" class="bolao-btn bolao-btn-primary bolao-btn-full">&#9917; Importar Jogos Copa 2026</button>
                        </form>
                        <form method="post">
                            <input type="hidden" name="_tipoAcao" value="resetar">
                            <button type="submit" onclick="return confirm('Resetar palpites e resultados?')" class="bolao-btn bolao-btn-warning bolao-btn-full">&#128260; Resetar Palpites/Resultados</button>
                        </form>
                        <form method="post">
                            <input type="hidden" name="_tipoAcao" value="limpar">
                            <button type="submit" onclick="return confirm('APAGAR TUDO? Esta acao nao pode ser desfeita!')" class="bolao-btn bolao-btn-danger bolao-btn-full">&#128465; Apagar Tudo</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Regras -->
        <div class="col-main">
            <div class="bolao-card">
                <div class="bolao-card-header">&#128203; REGRAS DE PONTUA&Ccedil;&Atilde;O</div>
                <div class="bolao-card-body">
                    <table class="bolao-table">
                        <thead>
                            <tr>
                                <th>PONTOS</th>
                                <th>DESCRI&Ccedil;&Atilde;O</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:12px;border-bottom:1px solid #f1f5f9;">
                                    <span style="background:linear-gradient(135deg,#c8e6c9,#a5d6a7);color:#1b5e20;padding:4px 12px;border-radius:6px;font-weight:800;font-size:14px;">10</span>
                                </td>
                                <td style="padding:12px;border-bottom:1px solid #f1f5f9;font-weight:500;">Placar exato</td>
                            </tr>
                            <tr>
                                <td style="padding:12px;border-bottom:1px solid #f1f5f9;">
                                    <span style="background:linear-gradient(135deg,#e3f2fd,#bbdefb);color:#1565c0;padding:4px 12px;border-radius:6px;font-weight:800;font-size:14px;">7</span>
                                </td>
                                <td style="padding:12px;border-bottom:1px solid #f1f5f9;font-weight:500;">Vencedor + saldo de gols</td>
                            </tr>
                            <tr>
                                <td style="padding:12px;border-bottom:1px solid #f1f5f9;">
                                    <span style="background:linear-gradient(135deg,#fff9c4,#fff176);color:#f57f17;padding:4px 12px;border-radius:6px;font-weight:800;font-size:14px;">5</span>
                                </td>
                                <td style="padding:12px;border-bottom:1px solid #f1f5f9;font-weight:500;">Acertou vencedor/empate</td>
                            </tr>
                            <tr>
                                <td style="padding:12px;border-bottom:1px solid #f1f5f9;">
                                    <span style="background:linear-gradient(135deg,#fff3e0,#ffe0b2);color:#e65100;padding:4px 12px;border-radius:6px;font-weight:800;font-size:14px;">2</span>
                                </td>
                                <td style="padding:12px;border-bottom:1px solid #f1f5f9;font-weight:500;">Acertou gols de 1 time</td>
                            </tr>
                            <tr>
                                <td style="padding:12px;">
                                    <span style="background:linear-gradient(135deg,#ffcdd2,#ef9a9a);color:#b71c1c;padding:4px 12px;border-radius:6px;font-weight:800;font-size:14px;">0</span>
                                </td>
                                <td style="padding:12px;font-weight:500;">Errou tudo</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
