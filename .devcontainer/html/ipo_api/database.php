<?php
/*
    database.php
    Versão: 2.1
*/

/**
 *  Abre uma conexão à base de dados
 * 
 *  Retorna a conexão
 * 
 */
function open_database() 
{
	try 
	{
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND=> 'SET NAMES utf8') );
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );

    return $conn;
	} catch (Exception $e) 
	{
		echo $e->getMessage();
		return null;
	}
}

/**
 *  Fecha uma conexão à base de dados
 * 
 */
function close_database($conn) {
	try 
	{
		$conn = null;
	} 
	catch (Exception $e) 
	{
		echo $e->getMessage();
	}
}

/**
 *  Executa um comando SQL - Select
 * 
 *  Retorna um array com as linhas resultantes do comando
 * 
 */
function ExecSqlSelect($strsql, $params = array()) 
{
	$basedados = open_database();
  $found = null;
  
  try 
  {
    $stmt = $basedados->prepare($strsql);
    $stmt->execute($params);   
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $found = array();
    while ($row = $stmt->fetch()) 
        $found[] = $row;
    $_SESSION['message'] = "";
  }  
  catch (Exception $e) 
  {
    $_SESSION['message'] = $e->GetMessage();
    $_SESSION['type'] = 'danger';
    RegistaErroLogbd($strsql, $e->GetMessage(), $params);
  }	
  close_database($basedados);
  return $found;    
}


/**
 *  Executa um comando SQL Não SELECT (Todos os Comandos excepto SELECT: INSERT, UPDATE, DELETE, etc.)
 */
function ExecSqlNonSelect($strsql, $params = array()) 
{
	$basedados = open_database();
  $ret = 0;
  
  try 
  {
    $stmt = $basedados->prepare($strsql);
    $stmt->execute($params);
    $ret = $stmt->rowCount();
    $_SESSION['message'] = "";
  }  
  catch (Exception $e) 
  {
    $_SESSION['message'] = $e->GetMessage();
    $_SESSION['type'] = 'danger';
    RegistaErroLogbd($strsql, $e->GetMessage(), $params);
  }	
  close_database($basedados);
  return($ret);
}

/**
 *  Executa um comando SQL - (INSERT com Auto Increment)
 * 
 *  Retorna o valor da chave (Auto Increment)
 * 
 */
function ExecSqlInsertRetAutoIncr($strsql, $params = array())
{
	$basedados = open_database();
  $found = null;
  
  try 
  {
    $stmt = $basedados->prepare($strsql);
    $stmt->execute($params);
    $ultimoregisto = $basedados->lastInsertId();   
    $_SESSION['message'] = "";
  }  
  catch (Exception $e) 
  {
    $_SESSION['message'] = $e->GetMessage();
    $_SESSION['type'] = 'danger';
    RegistaErroLogbd($strsql, $e->GetMessage(), $params);
  }	
  close_database($basedados);
  
  return $ultimoregisto;
}

/**
 *  Regista erros de SQL na tabela logbd
 *  Estrutura da tabela                                               */

// CREATE TABLE logbd (
//  id int(11) NOT NULL,
//  dataerro datetime NOT NULL DEFAULT current_timestamp(),
//  tagerro varchar(100) DEFAULT NULL,
//  ficheiro varchar(100) DEFAULT NULL,
//  comandosql text DEFAULT NULL,
//  dados text DEFAULT NULL,
//  msgerro text DEFAULT NULL
//  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
// ALTER TABLE logbd
//  ADD PRIMARY KEY (id);
// ALTER TABLE logbd
//  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

function RegistaErroLogbd($strsql, $msgerro, $params = array())
{
  if(!empty($_SESSION['email']))
    $tagerro = $_SESSION['email'];
  else
    $tagerro = "";

  $ficheiro = $_SERVER['PHP_SELF'];

  if(!empty($params))
    $dados = implode(";",$params);
  else
    $dados = "";

  $localstrsql = "INSERT INTO logbd(
    tagerro, 
    ficheiro, 
    comandosql, 
    dados, 
    msgerro
    ) VALUES (?,?,?,?,?)";

$localparams = array(
                      $tagerro, 
                      $ficheiro, 
                      $strsql, 
                      $dados, 
                      $msgerro
                    );

  $basedados = open_database();
  try 
  {
    $stmt = $basedados->prepare($localstrsql);
    $stmt->execute($localparams);
  }  
  catch (Exception $e) 
  {
    echo("  >>>> Erro no comando BD: " . $strsql);
    if ($dados != "") 
      echo("  >>>> Dados: " . $dados);
    echo("  >>>> Mensagem BD: " . $msgerro);
    exit();
  }	
  close_database($basedados);
}


/**
 *  Carrega as linhas de uma DropDown a partir de um array
 * 
 *  Registo (Array associativo com o Nome dos campos da Tabela) 
 * 
 */

function CarregaDropDownFromArray($array = null, $valorselecionado = null) 
{
  echo "<option value=''>Selecione uma opção...</option>";    
  
  foreach ($array as $linha)
  {
    $valor = array_shift($linha);
    $descricao = array_shift($linha);
    $sel = ($valor == $valorselecionado ? "Selected" : "");
    echo "<option value='" . $valor . "' " . $sel . " >" . $descricao . "</option>";
  } 
}


function CarregaDropDownMultiFromArray($array = null, $arrayselecionados = null) 
{
  foreach ($array as $linha)
  {
    $valor = array_shift($linha);
    $descricao = array_shift($linha);
    $sel = (in_array($valor, $arrayselecionados) ?  "Selected" : "");
    echo "<option value='" . $valor . "' " . $sel . " >" . $descricao . "</option>";
  } 
}

/**
 *  Carrega as linhas de uma DropDown a partir de um array (com grupos)
 * 
 *   Exemplo:
 *   SELECT Id, Descricao, Grupo FROM Tabela 
 * 
 */
function CarregaDropDownMultiFromArrayWithGroups($array = null, $arrayselecionados = null) 
{
  $grupoanterior="";
  $primeiro = true;
  foreach ($array as $linha)
  {
    $valor = array_shift($linha);
    $descricao = array_shift($linha);
    $grupo = array_shift($linha);
    if ($grupo != $grupoanterior)
    {
      if ($primeiro)
        $primeiro = false;
      else
        echo("</optgroup>");
      $grupoanterior = $grupo;
      echo "<optgroup label='" . $grupo . "'>";
    }
    $sel = (in_array($valor, $arrayselecionados) ?  "Selected" : "");
    echo "<option value='" . $valor . "' " . $sel . " >" . $descricao . "</option>";
  }
  if (!$primeiro)
    echo("</optgroup>");
}


/**
 *  Carrega Radio Buttons a partir de um array
 * 
 *  Registo (Array associativo com o Nome dos campos da Tabela) 
 * 
 */

function CarregaRadiosFromArray($name, $array = null, $valorselecionado = null, $addclasse ="") 
{
  global $modo;
  foreach ($array as $linha)
  {  
    $valor = array_shift($linha);
    $descricao = array_shift($linha);
    $sel = ($valor == $valorselecionado ? "checked" : "");
    $dis = ($modo == "view" ? "disabled" : "");
    echo "<br><input type='radio' class='ml-3 " . $addclasse . "' id='$valor' name='$name' value='$valor' $sel $dis ";
    echo "<label for='$valor'>&nbsp;$descricao</label>";

  }

}

/**
 *  valorregisto
 * 
 *   Devolve o valor do campo em $registo 
 */
function valorregisto($Nomecampo) 
{
  global $registo;

  if(isset($registo[$Nomecampo]))
    $ret = $registo[ $Nomecampo];
  else
    $ret = "";

  return $ret;

}

/**
 *   getvalue
 * 
 *   imprime o atributo value de $Nomecampo no array global $registo  
 * 
 */
function getvalue($Nomecampo, $typedatetime_local = false) 
{
  global $registo;

  if(isset($registo[$Nomecampo]))
    if (!$typedatetime_local)
      echo (" value = \"" . htmlspecialchars($registo[$Nomecampo]) . "\"");
    else
    {
      $data=date_create($registo[$Nomecampo]);
      $strdata = date_format($data,"Y-m-d\TH:i:s"); 
      echo (" value = \"" . $strdata . "\"");
  }
}

/**
 *   getchecked
 * 
 *   Ativa a checkbox se  o valor de $Nomecampo == 1 no array global $registo  
 * 
 */
function getchecked($Nomecampo) 
{
  global $registo;

  if(isset($registo[$Nomecampo]))
  {
    if ($registo[$Nomecampo] == 1)
      echo (" checked ");  
  }

}


/**
 *   getdisabled
 * 
 *   imprime o atributo disabled de acordo com a variável global $modo  
 * 
 */
function getdisabled($EChave = false, $EAutoIncr = false, $BloquearEdit = false) 
{
  global $modo;

  if($modo == "view")
    echo (" disabled ");
  elseif($modo == "add")
  {
    if($EAutoIncr == true)
      echo (" disabled ");
  }
  elseif($modo == "edit")
  {
    if($EChave == true)
      echo (" readonly ");
    else
    {
      if($BloquearEdit == true)
        echo (" readonly style='pointer-events: none;' ");
    }
  }
  else
    echo (" disabled ");
}




?>