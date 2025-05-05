<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once('../conexao.php');

// Função principal para alterar status
if (isset($_GET['acao_alt']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $acao = $_GET['acao_alt'];

    if ($acao === 'alterar_status' && isset($_GET['status'])) {
        $novo_status = $_GET['status'];
        $query = "UPDATE usuarios_projetos SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conexao, $query);
        mysqli_stmt_bind_param($stmt, "si", $novo_status, $id);
        mysqli_stmt_execute($stmt);
    }
    elseif ($acao === 'excluir') {
        $query = "DELETE FROM usuarios_projetos WHERE id = ?";
        $stmt = mysqli_prepare($conexao, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
    }

    // Recarrega a página após a ação
    echo "<script>window.location.href = 'painel_admin.php?acao=autorizacoes_projetos';</script>";
    exit;
}

// Configuração da paginação e consulta principal
$registros_por_pagina = 10;
$pagina_atual = $_GET['pagina'] ?? 1;
$offset = ($pagina_atual - 1) * $registros_por_pagina;

$query = "SELECT up.id, u.nip, u.nome AS usuario, p.nome AS projeto, 
                 up.data_autorizacao, up.status, p.imagem
          FROM usuarios_projetos up
          INNER JOIN usuarios u ON up.usuario_id = u.id
          INNER JOIN projetos p ON up.projeto_id = p.id
          ORDER BY up.data_autorizacao DESC
          LIMIT ? OFFSET ?";

$stmt = mysqli_prepare($conexao, $query);
mysqli_stmt_bind_param($stmt, "ii", $registros_por_pagina, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="container ml-4">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalAutorizar">
                Autorizar Usuário
            </button>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <form class="form-inline my-2 my-lg-0">
                <input name="pesquisa" class="form-control mr-sm-2" type="search" 
                    placeholder="Buscar NIP ou Projeto" 
                    value="<?= htmlspecialchars($_GET['pesquisa'] ?? '') ?>">
                <button class="btn btn-outline-secondary my-2 my-sm-0" type="submit">
                    <i class="fa fa-search"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>NIP</th>
                        <th>Usuário</th>
                        <th>Projeto</th>
                        <th>Data</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nip']) ?></td>
                        <td><?= htmlspecialchars($row['usuario']) ?></td>
                        <td>
                            <img src="../img/publicacoes/<?= htmlspecialchars($row['imagem']) ?>" 
                                 style="width:50px;height:50px;object-fit:cover;">
                            <?= htmlspecialchars($row['projeto']) ?>
                        </td>
                        <td><?= date('d/m/Y', strtotime($row['data_autorizacao'])) ?></td>
                        <td>
                            <span class="badge badge-<?= $row['status'] === 'Aprovada' ? 'success' : 'danger' ?>">
                                <?= $row['status'] ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($row['status'] === 'Aprovada'): ?>
                            <button onclick="alterarStatus(<?= $row['id'] ?>,'Desautorizada')" 
                                    class="btn btn-sm btn-danger">
                                <i class="fas fa-thumbs-down"></i>
                            </button>
                            <?php else: ?>
                            <button onclick="alterarStatus(<?= $row['id'] ?>,'Aprovada')" 
                                    class="btn btn-sm btn-success">
                                <i class="fas fa-thumbs-up"></i>
                            </button>
                            <?php endif; ?>
                            
                            <button onclick="excluirAutorizacao(<?= $row['id'] ?>)" 
                                    class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Autorização -->
<div class="modal fade" id="modalAutorizar">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>NIP do Usuário:</label>
                        <input type="text" name="nip" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Projeto:</label>
                        <select name="projeto_id" class="form-control" required>
                            <?php 
                            $projetos = mysqli_query($conexao, "SELECT * FROM projetos");
                            while ($p = mysqli_fetch_assoc($projetos)): ?>
                            <option value="<?= $p['id'] ?>"><?= $p['nome'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="autorizar" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function alterarStatus(id, status) {
    Swal.fire({
        title: 'Confirmar ação',
        text: `Deseja realmente ${status === 'Desautorizada' ? 'desautorizar' : 'aprovar'}?`,
        icon: 'warning',
        showCancelButton: true,
    }).then((r) => {
        if (r.isConfirmed) {
            window.location.href = `painel_admin.php?acao=autorizacoes_projetos&acao_alt=alterar_status&id=${id}&status=${status}`;
        }
    });
}

function excluirAutorizacao(id) {
    Swal.fire({
        title: 'Confirmar exclusão',
        text: 'Esta ação é permanente!',
        icon: 'error',
        showCancelButton: true,
    }).then((r) => {
        if (r.isConfirmed) {
            window.location.href = `painel_admin.php?acao=autorizacoes_projetos&acao_alt=excluir&id=${id}`;
        }
    });
}
</script>

<?php
// Processar nova autorização
if (isset($_POST['autorizar'])) {
    $nip = $_POST['nip'];
    $projeto_id = $_POST['projeto_id'];
    
    // Verifica se o usuário existe
    $usuario = mysqli_query($conexao, "SELECT id FROM usuarios WHERE nip = '$nip'");
    
    if (mysqli_num_rows($usuario) > 0) {
        $usuario_id = mysqli_fetch_assoc($usuario)['id'];
        
        // Verifica se já existe autorização para este usuário e projeto
        $verifica = mysqli_query($conexao, "SELECT id FROM usuarios_projetos 
                                          WHERE usuario_id = $usuario_id 
                                          AND projeto_id = $projeto_id");
        
        if (mysqli_num_rows($verifica) > 0) {
            echo "<script>Swal.fire('Este usuário já possui autorização para este projeto!')</script>";
        } else {
            $query = "INSERT INTO usuarios_projetos (usuario_id, projeto_id, data_autorizacao, status) 
                      VALUES (?, ?, CURDATE(), 'Aprovada')";
            $stmt = mysqli_prepare($conexao, $query);
            mysqli_stmt_bind_param($stmt, "ii", $usuario_id, $projeto_id);
            mysqli_stmt_execute($stmt);
            
            echo "<script>Swal.fire('Autorizado com sucesso!').then(() => location.reload())</script>";
        }
    } else {
        echo "<script>Swal.fire('NIP não encontrado!')</script>";
    }
}
?>

</body>
</html> 