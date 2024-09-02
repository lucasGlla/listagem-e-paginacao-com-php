<?php
include_once("../config/config.php");

// Receber a pagina
$pagina = filter_input(INPUT_GET, "pagina",FILTER_SANITIZE_NUMBER_INT);

if(!empty($pagina)){

    // Calcular o inicio da visualização
    $qnt_result_pg = 2;
    $inicio = ($pagina * $qnt_result_pg) - $qnt_result_pg;

    // Criação da consulta SQL
    $query_tickets = "SELECT id, titulo, estado, prioridade, local, setor, data_criacao, data_entrega FROM tickets ORDER BY id DESC LIMIT ?, ?";
    $stmt = $conexao->prepare($query_tickets);
    $stmt->bind_param("ii", $inicio, $qnt_result_pg);
    $stmt->execute();
    $result_tickets = $stmt->get_result();

    // Verificação se a consulta retornou resultados
    if ($result_tickets && $result_tickets->num_rows > 0) {
        $dados = "<table class='content-table'>";
        $dados .= "<thead>";

        $dados .= "<tr>";
        $dados .= "<th>ID</th>";
        $dados .= "<th>Título</th>";
        $dados .= "<th>Estado</th>";
        $dados .= "<th>Prioridade</th>";
        $dados .= "<th>Requisitante</th>";
        $dados .= "<th>Data de criação</th>";
        $dados .= "<th>Data de entrega</th>";
        $dados .= "<th>Ações</th>";
        $dados .= "</tr>";

        $dados .= "</thead>";

        $dados .= "<tbody>";
        while ($row_tickets = $result_tickets->fetch_assoc()) {
            $dados .= "<tr>";
            $dados .= "<td>" . htmlspecialchars($row_tickets['id']) . "</td>";
            $dados .= "<td>" . htmlspecialchars($row_tickets['titulo']) . "</td>";
            $dados .= "<td>" . htmlspecialchars($row_tickets['estado']) . "</td>";
            $dados .= "<td>" . htmlspecialchars($row_tickets['prioridade']) . "</td>";
            $dados .= "<td>" . htmlspecialchars($row_tickets['setor_usuario']) . "</td>";
            $dados .= "<td>" . htmlspecialchars($row_tickets['data_criacao']) . "</td>";
            $dados .= "<td>" . htmlspecialchars($row_tickets['data_entrega']) . "</td>";
            $dados .= "<td>
                <a href='visualizar.php?id=" . htmlspecialchars($row_tickets['id']) . "'>Visualizar</a> 
                <a href='editar.php?id=" . htmlspecialchars($row_tickets['id']) . "'>Editar</a> 
                <a href='apagar.php?id=" . htmlspecialchars($row_tickets['id']) . "'>Apagar</a>
            </td>";
            $dados .= "</tr>";
        }
        $dados .= "</tbody>";
        $dados .= "</table>";

        // Paginação
        $query_pg = "SELECT COUNT(id) AS num_result FROM tickets";
        $result_pg = $conexao->prepare($query_pg);
        $result_pg->execute();
        $result_pg = $result_pg->get_result();
        $row_pg = $result_pg->fetch_assoc();

        // Quantidade de paginas
        $quantidade_pg = ceil($row_pg['num_result'] / $qnt_result_pg);

        $max_links = 2;

        $dados .= "<div class='pagination'>";

        $dados .= "<a href='#' onclick='listarChamados(1)'>Primeira </a>";

        for($pag_ant = $pagina - $max_links; $pag_ant <= $pagina - 1; $pag_ant++){
            if($pag_ant >= 1){
                $dados .= "<a href='#' onclick='listarChamados($pag_ant)'>$pag_ant </a>";
            }
        }

        $dados .= $pagina;

        for($pag_dep = $pagina + 1; $pag_dep <= $pagina + $max_links; $pag_dep++){
            if($pag_dep <= $quantidade_pg){
                $dados .= "<a href='#' onclick='listarChamados($pag_dep)'>$pag_dep </a>";
            }
        }

        $dados .= "<a href='#' onclick='listarChamados($quantidade_pg)'>Ultima </a>";

        $dados .= "</div>";

        $retorna = ['status' => true, 'dados' => $dados, 'quantidade_pg' => $quantidade_pg];
    } else {
        $retorna = ['status' => false, 'msg' => "<p>Nenhum chamado encontrado!</p>"];
    }
} else{
    $retorna = ['status' => false, 'msg' => "<p>Sem pagina disponivel!</p>"];
}

// Retorno em formato JSON
echo json_encode($retorna);
?>
