<?php

require_once 'config.php';
require_once 'database.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

function validacao($input)
{
    global $data, $numfaltas, $descfaltas, $aprovado, $matricula;

    $data = trim($input['data'] ?? '');
    $numfaltas = trim($input['numfaltas'] ?? '');
    $descfaltas = trim($input['descfaltas'] ?? '');
    $aprovado = isset($input['aprovado']) ? (int)$input['aprovado'] : null;
    $matricula = trim($input['matricula'] ?? '');

    if (empty($data) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
        return 'Data é um campo obrigatório e deve estar no formato YYYY-MM-DD.';
    }
    if ( !is_numeric($numfaltas) || $numfaltas < 0 ) {
        return 'NumFaltas deve ser numérico e não negativo.';
    }
    if (empty($descfaltas)) {
        return 'DescFaltas é um campo obrigatório.';
    }
    if (!isset($aprovado) || ($aprovado !== 0 && $aprovado !== 1)) {
        return 'Aprovado é um campo obrigatório e deve ser 0 ou 1.';
    }
    if (empty($matricula)) {
        return 'Matricula é um campo obrigatório.';
    }
    // Check if matricula exists in veiculos
    $sql = "select count(*) as count from veiculos where matricula = ?";
    $result = ExecSqlSelect($sql, [$matricula]);
    if (!$result || $result[0]['count'] == 0) {
        return 'Matricula não existe na tabela veiculos.';
    }
    return '';
}

function validaDelete($id)
{
    // No dependent tables for inspecoes
    return '';
}

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$getMatricula = isset($_GET['matricula']) ? $_GET['matricula'] : null;


switch ($method) {
    case 'GET':
        if ($id) {
            $sql = "select * from inspecoes where numins = ?";
            $result = ExecSqlSelect($sql, [$id]);
            if ($_SESSION['message'] != "") {
                echo json_encode(['success' => false, 'message' => 'Falha no comando à base de dados' ]);
            } elseif ($result) {
                echo json_encode(['success' => true, 'data' => $result[0]]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Registo não encontrado']);
            }
        } elseif ($getMatricula) {
            // Devolver todos os registos de uma matrícula
            $sql = "select * from inspecoes where matricula = ?";
            $result = ExecSqlSelect($sql, [$getMatricula]);
            if ($_SESSION['message'] != "") {
                echo json_encode(['success' => false, 'message' => 'Falha no comando à base de dados' ]);
            }
            else {
                echo json_encode(['success' => true, 'data' => $result]);
            }            
        } else {
            $sql = "select * from inspecoes";
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
    $sql = "insert into inspecoes (data, numfaltas, descfaltas, aprovado, matricula) values (?, ?, ?, ?, ?)";
    $newId = ExecSqlInsertRetAutoIncr($sql, [$data, $numfaltas, $descfaltas, $aprovado, $matricula]);
        if ($_SESSION['message'] != "") {
            echo json_encode(['success' => false, 'message' => 'Falha no comando à base de dados' ]);
        } elseif ($newId) {
            echo json_encode(['success' => true, 'message' => 'Registo criado', 'id' => $newId]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Falha ao criar registo']);
        }
        break;

    case 'PUT':
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID necessário']);
            break;
        }
        $input = json_decode(file_get_contents('php://input'), true);
        $mensagem = validacao($input);
        if ($mensagem != '') {
            echo json_encode(['success' => false, 'message' => $mensagem]);
            break;
        }
    $sql = "update inspecoes set data = ?, numfaltas = ?, descfaltas = ?, aprovado = ?, matricula = ? where numins = ?";
    ExecSqlNonSelect($sql, [$data, $numfaltas, $descfaltas, $aprovado, $matricula, $id]);
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
    $sql = "delete from inspecoes where numins = ?";
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
