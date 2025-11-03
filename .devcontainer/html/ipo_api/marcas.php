<?php

require_once 'config.php';
require_once 'database.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

function validacao($input)
{
    global $marca;

    $marca = trim($input['marca'] ?? '');

    if (empty($marca)) {
        return 'Marca é um campo obrigatório.';
    }
    return '';
}

function validaDelete($id)
{
    $sql = "select count(*) as count from veiculos where codmarca = ?";
    $result = ExecSqlSelect($sql, [$id]);
    if ($result && $result[0]['count'] > 0) {
        return 'A marca não pode ser eliminada porque tem registos relacionados na tabela veículos.';
    }
    return '';
}

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

switch ($method) {
    case 'GET':
        if ($id) {
            $sql = "select * from marcas where codmarca = ?";
            $result = ExecSqlSelect($sql, [$id]);
            if ($_SESSION['message'] != "") {
                echo json_encode(['success' => false, 'message' => 'Falha no comando à base de dados' ]);
            } elseif ($result) {
                echo json_encode(['success' => true, 'data' => $result[0]]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Registo não encontrado']);
            }
        } else {
            $sql = "select * from marcas";
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
    $sql = "insert into marcas (marca) values (?)";
    $newId = ExecSqlInsertRetAutoIncr($sql, [$marca]);
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
    $sql = "update marcas set marca = ? where codmarca = ?";
    ExecSqlNonSelect($sql, [$marca, $id]);
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
    $sql = "delete from marcas where codmarca = ?";
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
