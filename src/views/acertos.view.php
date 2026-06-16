<?php
// Buscar todos os palpites que acertaram o placar exato
$sql = "SELECT bp.palpite_gols1, bp.palpite_gols2, bp.pontos, bpar.nome as participante_nome, 
        bj.time1, bj.time2, bj.gols1, bj.gols2, bj.data_jogo
        FROM bolao_palpites bp 
        INNER JOIN bolao_participantes bpar ON bp.participante_id = bpar.id 
        INNER JOIN bolao_jogos bj ON bp.jogo_id = bj.id 
        WHERE bj.finalizado = 1 AND bp.palpite_gols1 = bj.gols1 AND bp.palpite_gols2 = bj.gols2
        ORDER BY bj.data_jogo DESC, bpar.nome ASC";
$rAcertos = mysqli_query($connection, $sql);
$acertos = [];
if ($rAcertos) { while ($row = mysqli_fetch_assoc($rAcertos)) $acertos[] = $row; }
?>
<?php include(__DIR__ . '/_css.php'); ?>

<div class="bolao-wrapper">
    <div class="bolao-header">
        <h2>&#127881; ACERTOS</h2>
        <p>Quem acertou o placar exato</p>
    </div>

    <div class="bolao-nav">
        <a href="?">&#127942; Ranking</a>
        <a href="?_route=jogos">&#9917; Jogos</a>
        <a href="?_route=palpites">&#128221; Palpites</a>
        <a href="?_route=participantes">&#128101; Participantes</a>
        <a href="?_route=acertos" class="active">&#127881; Acertos</a>
        <a href="?_route=config">&#9881; Config</a>
    </div>

    <div class="bolao-card">
        <div class="bolao-card-header">&#127881; PALPITES EXATOS (<?php echo count($acertos); ?>)</div>
        <div class="bolao-card-body" style="padding:0;overflow-x:auto;">
            <?php if(empty($acertos)): ?>
            <div class="bolao-empty">
                <p>Nenhum acerto ainda.</p>
                <span>Quando algu&eacute;m acertar o placar exato, aparece aqui.</span>
            </div>
            <?php else: ?>
            <table class="bolao-table">
                <thead>
                    <tr>
                        <th>DATA</th>
                        <th>JOGO</th>
                        <th style="text-align:center;">PLACAR</th>
                        <th>GANHADOR</th>
                        <th style="text-align:center;">PALPITE</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($acertos as $a): ?>
                <tr>
                    <td style="padding:12px;border-bottom:1px solid #f1f5f9;font-size:12px;color:#718096;"><?php echo date('d/m/Y', strtotime($a['data_jogo'])); ?></td>
                    <td style="padding:12px;border-bottom:1px solid #f1f5f9;font-weight:700;"><?php echo $a['time1'] . ' x ' . $a['time2']; ?></td>
                    <td style="padding:12px;border-bottom:1px solid #f1f5f9;text-align:center;">
                        <span style="background:linear-gradient(135deg,#1b5e20,#2e7d32);color:white;padding:4px 12px;border-radius:6px;font-weight:800;font-size:14px;"><?php echo $a['gols1'] . ' - ' . $a['gols2']; ?></span>
                    </td>
                    <td style="padding:12px;border-bottom:1px solid #f1f5f9;">
                        <span style="display:inline-flex;align-items:center;gap:6px;">
                            <span style="font-size:18px;">&#127942;</span>
                            <strong style="color:#1b5e20;"><?php echo htmlspecialchars($a['participante_nome']); ?></strong>
                        </span>
                    </td>
                    <td style="padding:12px;border-bottom:1px solid #f1f5f9;text-align:center;">
                        <span class="palpite-badge palpite-acerto"><?php echo $a['palpite_gols1'] . ' x ' . $a['palpite_gols2']; ?> &#10004;</span>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>
