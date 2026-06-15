<?php
$acessos = obterAcessos($connection);
$detalhe = isset($_GET['cliente_id']) ? obterAcessosDetalhado($connection, intval($_GET['cliente_id'])) : null;
$totalGeral = 0;
foreach ($acessos as $a) $totalGeral += $a['total_acessos'];
?>
<head><link href="src/css/bolao.css" rel="stylesheet" type="text/css"></head>

<div class="bolao-wrapper">
    <div class="bolao-header">
        <h2>&#128202; ACESSOS DO PAINEL CLIENTE</h2>
        <p>Quem acessou o bol&atilde;o e quantas vezes</p>
    </div>

    <div class="bolao-nav">
        <a href="?">&#127942; Ranking</a>
        <a href="?_route=jogos">&#9917; Jogos</a>
        <a href="?_route=palpites">&#128221; Palpites</a>
        <a href="?_route=participantes">&#128101; Participantes</a>
        <a href="?_route=acessos" class="active">&#128202; Acessos</a>
        <a href="?_route=config">&#9881; Config</a>
    </div>

    <!-- Resumo -->
    <div class="bolao-card" style="margin-bottom:18px;">
        <div class="bolao-card-header">&#128200; RESUMO</div>
        <div class="bolao-card-body">
            <div style="display:flex;gap:20px;flex-wrap:wrap;">
                <div style="text-align:center;flex:1;min-width:120px;">
                    <div style="font-size:28px;font-weight:800;color:#2e7d32;"><?php echo count($acessos); ?></div>
                    <div style="font-size:12px;color:#666;">Clientes acessaram</div>
                </div>
                <div style="text-align:center;flex:1;min-width:120px;">
                    <div style="font-size:28px;font-weight:800;color:#1565c0;"><?php echo $totalGeral; ?></div>
                    <div style="font-size:12px;color:#666;">Total de acessos</div>
                </div>
            </div>
        </div>
    </div>

    <?php if($detalhe): 
        $nomeCliente = $detalhe[0]['nome'] ?? 'Cliente';
    ?>
    <!-- Detalhe de acessos do cliente -->
    <div class="bolao-card" style="margin-bottom:18px;">
        <div class="bolao-card-header">&#128100; Hist&oacute;rico: <?php echo htmlspecialchars($nomeCliente); ?> <a href="?_route=acessos" style="float:right;font-size:12px;color:#2e7d32;">&larr; Voltar</a></div>
        <div class="bolao-card-body">
            <table class="bolao-table" style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="background:#f8f9fa;"><th style="padding:8px;text-align:left;">Data/Hora</th><th style="padding:8px;text-align:left;">IP</th></tr>
                </thead>
                <tbody>
                    <?php foreach($detalhe as $d): ?>
                    <tr style="border-bottom:1px solid #eee;">
                        <td style="padding:8px;"><?php echo date('d/m/Y H:i', strtotime($d['data_acesso'])); ?></td>
                        <td style="padding:8px;"><?php echo htmlspecialchars($d['ip']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Lista geral -->
    <div class="bolao-card">
        <div class="bolao-card-header">&#128101; CLIENTES QUE ACESSARAM</div>
        <div class="bolao-card-body">
            <?php if(empty($acessos)): ?>
            <p style="text-align:center;color:#999;padding:20px;">Nenhum acesso registrado ainda.</p>
            <?php else: ?>
            <table class="bolao-table" style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="background:#f8f9fa;">
                        <th style="padding:8px;text-align:left;">Cliente</th>
                        <th style="padding:8px;text-align:center;">Acessos</th>
                        <th style="padding:8px;text-align:center;">&Uacute;ltimo acesso</th>
                        <th style="padding:8px;text-align:center;">Detalhes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($acessos as $a): ?>
                    <tr style="border-bottom:1px solid #eee;">
                        <td style="padding:8px;font-weight:500;"><?php echo htmlspecialchars($a['nome']); ?></td>
                        <td style="padding:8px;text-align:center;">
                            <span style="background:#e8f5e9;color:#2e7d32;padding:3px 10px;border-radius:12px;font-weight:700;font-size:12px;">
                                <?php echo $a['total_acessos']; ?>x
                            </span>
                        </td>
                        <td style="padding:8px;text-align:center;font-size:12px;color:#666;">
                            <?php echo date('d/m/Y H:i', strtotime($a['ultimo_acesso'])); ?>
                        </td>
                        <td style="padding:8px;text-align:center;">
                            <a href="?_route=acessos&cliente_id=<?php echo $a['cliente_id']; ?>" style="color:#1565c0;font-size:12px;text-decoration:none;">Ver</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>
