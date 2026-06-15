<?php
function criarTabelas($c) {
    mysqli_query($c, "CREATE TABLE IF NOT EXISTS bolao_jogos (
        id INT AUTO_INCREMENT PRIMARY KEY, fase VARCHAR(20) DEFAULT 'grupos', grupo VARCHAR(5) DEFAULT '',
        time1 VARCHAR(50) NOT NULL, time2 VARCHAR(50) NOT NULL,
        bandeira1 VARCHAR(10) DEFAULT '', bandeira2 VARCHAR(10) DEFAULT '',
        data_jogo DATETIME NOT NULL, local_jogo VARCHAR(100) DEFAULT '',
        gols1 INT DEFAULT NULL, gols2 INT DEFAULT NULL, finalizado TINYINT(1) DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    mysqli_query($c, "CREATE TABLE IF NOT EXISTS bolao_palpites (
        id INT AUTO_INCREMENT PRIMARY KEY, jogo_id INT NOT NULL, participante_id INT NOT NULL,
        palpite_gols1 INT DEFAULT 0, palpite_gols2 INT DEFAULT 0, pontos INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, UNIQUE KEY uq (jogo_id, participante_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    mysqli_query($c, "CREATE TABLE IF NOT EXISTS bolao_participantes (
        id INT AUTO_INCREMENT PRIMARY KEY, cliente_id INT DEFAULT NULL,
        nome VARCHAR(100) NOT NULL, telefone VARCHAR(20) DEFAULT '', pontos_total INT DEFAULT 0,
        ativo TINYINT(1) DEFAULT 1, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
}

function adicionarJogo($c, $d) {
    $sql = "INSERT INTO bolao_jogos (fase,grupo,time1,time2,bandeira1,bandeira2,data_jogo,local_jogo) VALUES (
        '".mysqli_real_escape_string($c,$d['fase'])."','".mysqli_real_escape_string($c,$d['grupo']??'')."',
        '".mysqli_real_escape_string($c,$d['time1'])."','".mysqli_real_escape_string($c,$d['time2'])."',
        '".mysqli_real_escape_string($c,$d['bandeira1']??'')."','".mysqli_real_escape_string($c,$d['bandeira2']??'')."',
        '".mysqli_real_escape_string($c,$d['data_jogo'])."','".mysqli_real_escape_string($c,$d['local_jogo']??'')."')";
    return mysqli_query($c, $sql);
}

function registrarResultado($c, $jogoId, $g1, $g2) {
    mysqli_query($c, "UPDATE bolao_jogos SET gols1=$g1, gols2=$g2, finalizado=1 WHERE id=".intval($jogoId));
    $r = mysqli_query($c, "SELECT * FROM bolao_palpites WHERE jogo_id=".intval($jogoId));
    if ($r) { while ($p = mysqli_fetch_assoc($r)) {
        $pts = calcularPontos($g1,$g2,$p['palpite_gols1'],$p['palpite_gols2']);
        mysqli_query($c, "UPDATE bolao_palpites SET pontos=$pts WHERE id=".$p['id']);
        atualizarPontosTotal($c, $p['participante_id']);
    }}
}

function calcularPontos($r1,$r2,$p1,$p2) {
    // Só conta se acertou o placar exato
    if ($r1==$p1 && $r2==$p2) return 1;
    return 0;
}

function atualizarPontosTotal($c, $pid) {
    mysqli_query($c, "UPDATE bolao_participantes SET pontos_total=(SELECT COALESCE(SUM(pontos),0) FROM bolao_palpites WHERE participante_id=$pid) WHERE id=$pid");
}

function registrarPalpite($c, $jid, $pid, $g1, $g2) {
    // Garantir que são inteiros (0 é válido)
    $g1 = intval($g1);
    $g2 = intval($g2);
    $jid = intval($jid);
    $pid = intval($pid);
    
    // Verificar se o participante está inadimplente (fatura vencida em aberto)
    $rCli = mysqli_query($c, "SELECT cliente_id FROM bolao_participantes WHERE id = $pid");
    if ($rCli && $cliRow = mysqli_fetch_assoc($rCli)) {
        if ($cliRow['cliente_id']) {
            $cid = intval($cliRow['cliente_id']);
            $rFat = mysqli_query($c, "SELECT COUNT(*) as qtd FROM sis_titulo WHERE login = (SELECT login FROM sis_cliente WHERE id = $cid) AND status = 'aberto' AND datavenc < CURDATE()");
            if ($rFat && $fRow = mysqli_fetch_assoc($rFat)) {
                if (intval($fRow['qtd']) > 0) return false; // cliente inadimplente
            }
        }
    }
    
    // Verificar se o jogo ainda aceita palpites (até 10 minutos antes do início)
    $rJ = mysqli_query($c, "SELECT data_jogo, finalizado FROM bolao_jogos WHERE id = $jid");
    if ($rJ && $j = mysqli_fetch_assoc($rJ)) {
        if ($j['finalizado']) return false; // jogo já finalizado
        $limite = strtotime($j['data_jogo']) - 600; // 10 minutos antes
        if (time() > $limite) return false; // passou do horário limite
    } else {
        return false; // jogo não encontrado
    }
    
    // Verificar se já existe palpite
    $rExiste = mysqli_query($c, "SELECT id FROM bolao_palpites WHERE jogo_id = $jid AND participante_id = $pid");
    if ($rExiste && mysqli_num_rows($rExiste) > 0) {
        // Atualizar
        $row = mysqli_fetch_assoc($rExiste);
        return mysqli_query($c, "UPDATE bolao_palpites SET palpite_gols1 = $g1, palpite_gols2 = $g2 WHERE id = " . $row['id']);
    } else {
        // Inserir
        return mysqli_query($c, "INSERT INTO bolao_palpites (jogo_id, participante_id, palpite_gols1, palpite_gols2) VALUES ($jid, $pid, $g1, $g2)");
    }
}

function adicionarParticipante($c, $nome, $tel='', $cid=null) {
    return mysqli_query($c, "INSERT INTO bolao_participantes (nome,telefone,cliente_id) VALUES ('".mysqli_real_escape_string($c,$nome)."','".mysqli_real_escape_string($c,$tel)."',".($cid?intval($cid):"NULL").")");
}

function obterRanking($c) {
    $r = mysqli_query($c, "SELECT * FROM bolao_participantes WHERE ativo=1 ORDER BY pontos_total DESC, nome ASC");
    $a=[]; if($r) while($row=mysqli_fetch_assoc($r)) $a[]=$row; return $a;
}

function obterJogos($c, $fase=null) {
    $sql = "SELECT * FROM bolao_jogos";
    if ($fase && $fase!=='todos') $sql .= " WHERE fase='".mysqli_real_escape_string($c,$fase)."'";
    $sql .= " ORDER BY data_jogo ASC";
    $r = mysqli_query($c, $sql); $a=[]; if($r) while($row=mysqli_fetch_assoc($r)) $a[]=$row; return $a;
}

function obterPalpitesJogo($c, $jid) {
    $r = mysqli_query($c, "SELECT bp.*,bpar.nome as pnome FROM bolao_palpites bp INNER JOIN bolao_participantes bpar ON bp.participante_id=bpar.id WHERE bp.jogo_id=".intval($jid)." ORDER BY bp.pontos DESC");
    $a=[]; if($r) while($row=mysqli_fetch_assoc($r)) $a[]=$row; return $a;
}

function listarParticipantes($c) {
    $r = mysqli_query($c, "SELECT * FROM bolao_participantes ORDER BY nome ASC");
    $a=[]; if($r) while($row=mysqli_fetch_assoc($r)) $a[]=$row; return $a;
}

function excluirParticipante($c,$id) { mysqli_query($c,"DELETE FROM bolao_palpites WHERE participante_id=".intval($id)); return mysqli_query($c,"DELETE FROM bolao_participantes WHERE id=".intval($id)); }
function excluirJogo($c,$id) { mysqli_query($c,"DELETE FROM bolao_palpites WHERE jogo_id=".intval($id)); return mysqli_query($c,"DELETE FROM bolao_jogos WHERE id=".intval($id)); }

function importarJogosCopa2026($c) {
    $r=mysqli_query($c,"SELECT COUNT(*) as t FROM bolao_jogos"); $row=mysqli_fetch_assoc($r); if($row['t']>0) return 0;
    $jogos=[
        ['grupos','A','EUA','A definir','2026-06-11 18:00','MetLife, Nova York'],
        ['grupos','A','Mexico','A definir','2026-06-11 21:00','Azteca, Mexico'],
        ['grupos','B','Brasil','A definir','2026-06-12 18:00','SoFi, Los Angeles'],
        ['grupos','C','Argentina','A definir','2026-06-13 18:00','Hard Rock, Miami'],
        ['grupos','D','Alemanha','A definir','2026-06-14 18:00','A definir'],
        ['grupos','E','Franca','A definir','2026-06-15 18:00','A definir'],
        ['grupos','F','Inglaterra','A definir','2026-06-16 18:00','A definir'],
        ['grupos','F','Espanha','A definir','2026-06-16 21:00','A definir'],
    ];
    $n=0; foreach($jogos as $j) { mysqli_query($c,"INSERT INTO bolao_jogos (fase,grupo,time1,time2,data_jogo,local_jogo) VALUES ('{$j[0]}','{$j[1]}','{$j[2]}','{$j[3]}','{$j[4]}','{$j[5]}')"); $n++; }
    return $n;
}
