<?php
include_once('../conexao.php');
session_start();
$nip_usuario = $_SESSION['nip_usuario'];

// Verifica se um projeto específico foi selecionado
$projeto_selecionado = isset($_GET['projeto_id']) ? $_GET['projeto_id'] : null;

// Se um projeto foi selecionado, busca suas publicações
if ($projeto_selecionado) {
    $query_publicacoes = "SELECT p.*, pr.nome as nome_projeto 
                         FROM publicacoes p 
                         INNER JOIN projetos pr ON p.projeto = pr.id 
                         INNER JOIN usuarios_projetos up ON up.projeto_id = pr.id 
                         WHERE up.usuario_id = (SELECT id FROM usuarios WHERE nip = ?) 
                         AND up.status = 'Aprovada' 
                         AND pr.id = ?";
    
    $stmt = mysqli_prepare($conexao, $query_publicacoes);
    mysqli_stmt_bind_param($stmt, "si", $nip_usuario, $projeto_selecionado);
    mysqli_stmt_execute($stmt);
    $result_publicacoes = mysqli_stmt_get_result($stmt);
    
    // Busca informações do projeto selecionado
    $query_projeto = "SELECT * FROM projetos WHERE id = ?";
    $stmt = mysqli_prepare($conexao, $query_projeto);
    mysqli_stmt_bind_param($stmt, "i", $projeto_selecionado);
    mysqli_stmt_execute($stmt);
    $projeto = mysqli_fetch_assoc($stmt->get_result());
} else {
    // Busca todos os projetos autorizados para o usuário
    $query_projetos = "SELECT p.* 
                      FROM projetos p 
                      INNER JOIN usuarios_projetos up ON up.projeto_id = p.id 
                      WHERE up.usuario_id = (SELECT id FROM usuarios WHERE nip = ?) 
                      AND up.status = 'Aprovada'";
    
    $stmt = mysqli_prepare($conexao, $query_projetos);
    mysqli_stmt_bind_param($stmt, "s", $nip_usuario);
    mysqli_stmt_execute($stmt);
    $result_projetos = mysqli_stmt_get_result($stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        .card-projeto {
            transition: transform 0.3s;
            cursor: pointer;
            margin-bottom: 20px;
        }
        .card-projeto:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        .table-publicacoes {
            margin-top: 20px;
        }
        .btn-voltar {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container ml-4">
    <?php if ($projeto_selecionado): ?>
        <!-- Exibe as publicações do projeto selecionado -->
        <button class="btn btn-secondary btn-voltar" onclick="window.location.href='painel_usuario.php?acao=projetos_publicacoes'">
            <i class="fas fa-arrow-left"></i> Voltar aos Projetos
        </button>
        
        <h2>Publicações do Projeto: <?= htmlspecialchars($projeto['nome']) ?></h2>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>Publicação</th>
                        <th>Descrição</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($publicacao = mysqli_fetch_assoc($result_publicacoes)): ?>
                        <tr>
                            <td>
                                <img src="../img/publicacoes/<?= htmlspecialchars($publicacao['imagem']) ?>" 
                                     style="width:50px;height:50px;object-fit:cover;" class="mr-2">
                                <?= htmlspecialchars($publicacao['nome']) ?>
                            </td>
                            <td><?= htmlspecialchars($publicacao['descricao']) ?></td>
                            <td><?= date('d/m/Y', strtotime($publicacao['data'])) ?></td>
                            <td>
                                <a href="painel_usuario.php?acao=publicacoes&func=aulas&id=<?= $publicacao['id'] ?>" 
                                   class="btn btn-primary btn-sm">
                                    <i class="fas fa-book-reader"></i> Acessar
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <!-- Exibe os cards dos projetos -->
        <h2>Meus Projetos</h2>
        <div class="row">
            <?php while ($projeto = mysqli_fetch_assoc($result_projetos)): ?>
                <div class="col-md-4">
                    <div class="card card-projeto" onclick="window.location.href='painel_usuario.php?acao=projetos_publicacoes&projeto_id=<?= $projeto['id'] ?>'">
                        <img src="../img/publicacoes/<?= htmlspecialchars($projeto['imagem']) ?>" 
                             class="card-img-top" alt="<?= htmlspecialchars($projeto['nome']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($projeto['nome']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($projeto['descricao']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html> 