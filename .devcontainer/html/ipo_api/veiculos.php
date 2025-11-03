<?php

require_once 'config.php';
require_once 'database.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

function validacao($input )
{
    global  $datalivrete, $anofabrico, $codcli, $codmarca;
        
    $datalivrete = trim($input['datalivrete'] ?? '');
    $anofabrico = trim($input['anofabrico'] ?? '');
    $codcli = trim($input['codcli'] ?? '');
    $codmarca = trim($input['codmarca'] ?? '');

    if (empty($datalivrete) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $datalivrete)) {
        return 'DataLivrete é um campo obrigatório e deve estar no formato YYYY-MM-DD.';
    }
    if (empty($anofabrico) || !is_numeric($anofabrico)) {
        return 'AnoFabrico é um campo obrigatório e deve ser numérico.';
    }
    if (empty($codcli) || !is_numeric($codcli)) {
        return 'CodCli é um campo obrigatório e deve ser numérico.';
    }
    if (empty($codmarca) || !is_numeric($codmarca)) {
        return 'CodMarca é um campo obrigatório e deve ser numérico.';
    }
    // Check if codcli exists in clientes
    $sql = "select count(*) as count from clientes where codcli = ?";
    $result = ExecSqlSelect($sql, [$codcli]);
    if (!$result || $result[0]['count'] == 0) {
        return 'CodCli não existe na tabela clientes.';
    }
    // Check if codmarca exists in marcas
    $sql = "select count(*) as count from marcas where codmarca = ?";
    $result = ExecSqlSelect($sql, [$codmarca]);
    if (!$result || $result[0]['count'] == 0) {
        return 'CodMarca não existe na tabela marcas.';
    }
    return '';
}

function validaDelete($matricula)
{
    $sql = "select count(*) as count from inspecoes where matricula = ?";
    $result = ExecSqlSelect($sql, [$matricula]);
    if ($result && $result[0]['count'] > 0) {
        return 'O veículo não pode ser eliminado porque tem registos relacionados na tabela inspeções.';
    }
    return '';
}

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? trim($_GET['id']) : null; // Matricula is string

switch ($method) {
    case 'GET':
        if ($id) {
            $sql = "select * from veiculos where matricula = ?";
            $result = ExecSqlSelect($sql, [$id]);
            if ($_SESSION['message'] != "") {
                echo json_encode(['success' => false, 'message' => 'Falha no comando à base de dados' ]);
            } elseif ($result) {
                echo json_encode(['success' => true, 'data' => $result[0]]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Registo não encontrado']);
            }
        } else {
            $sql = "select * from veiculos";
            $result = ExecSqlSelect($sql);
            if ($_SESSION['message'] != "") {
                echo json_encode(['success' => false, 'message' => 'Falha no comando à base de dados' ]);
            } else {
                echo json_encode(['success' => true, 'data' => $result]);
            }
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        $mensagem = validacao($input);
        if ($mensagem != '') {
            echo json_encode(['success' => false, 'message' => $mensagem]);
            break;
        }
        $matricula = trim($input['matricula'] ?? '');
        if ($matricula == '') {
            echo json_encode(['success' => false, 'message' => 'A Matricula não pode ser vazia.']);
            break;
        }
        // Check if matricula already exists
        $sql = "select count(*) as count from veiculos where matricula = ?";
        $result = ExecSqlSelect($sql, [$input['matricula']]);
        if ($result && $result[0]['count'] > 0) {
            echo json_encode(['success' => false, 'message' => 'Matricula já existe.']);
            break;
        }
        $sql = "insert into veiculos (matricula, datalivrete, anofabrico, codcli, codmarca) values (?, ?, ?, ?, ?)";
        ExecSqlNonSelect($sql, [$matricula, $datalivrete, $anofabrico, $codcli, $codmarca]);
        if ($_SESSION['message'] != "") {
            echo json_encode(['success' => false, 'message' => 'Falha no comando à base de dados' ]);
        } else {
            echo json_encode(['success' => true, 'message' => 'Registo criado', 'id' => $matricula]);
        }
        break;

    case 'PUT':
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID necessário']);
            break;
        }
        $input = json_decode(file_get_contents('php://input'), true);
        $mensagem = validacao($input, $method, $id);
        if ($mensagem != '') {
            echo json_encode(['success' => false, 'message' => $mensagem]);
            break;
        }
        $sql = "update veiculos set datalivrete = ?, anofabrico = ?, codcli = ?, codmarca = ? where matricula = ?";
        ExecSqlNonSelect($sql, [$datalivrete, $anofabrico, $codcli, $codmarca, $id]);
        if ($_SESSION['message'] != "") {
            echo json_encode(['success' => false, 'message' => 'Falha no comando à base de dados' ]);
        } else {
            echo json_encode(['success' => true, 'message' => 'Registo atualizado']);
        }
        break;

    case 'DELETE':
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID necessário']);
            break;
        }
        $mensagem = validaDelete($id);
        if ($mensagem != '') {
            echo json_encode(['success' => false, 'message' => $mensagem]);
            break;
        }
    $sql = "delete from veiculos where matricula = ?";
    ExecSqlNonSelect($sql, [$id]);
        if ($_SESSION['message'] != "") {
            echo json_encode(['success' => false, 'message' => 'Falha no comando à base de dados' ]);
        } else {
            echo json_encode(['success' => true, 'message' => 'Registo eliminado']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Método não permitido']);
        break;
}
