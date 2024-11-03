<?php
// INCLUE FUNCOES DE ADDONS -----------------------------------------------------------------------
include('addons.class.php');

// VERIFICA SE O USUARIO ESTA LOGADO --------------------------------------------------------------
session_name('mka');
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['mka_logado']) && !isset($_SESSION['MKA_Logado'])) exit('Acesso negado... <a href="/admin/login.php">Fazer Login</a>');
// VERIFICA SE O USUARIO ESTA LOGADO --------------------------------------------------------------

// Assuming $Manifest is defined somewhere before this code
$manifestTitle = $Manifest->{'name'} ?? '';
$manifestVersion = $Manifest->{'version'} ?? '';
$manifestAuthor = $Manifest->{'author'} ?? '';
?>

<!DOCTYPE html>
<html lang="pt-BR" class="has-navbar-fixed-top">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta charset="iso-8859-1">
<title>MK-AUTH :: <?php echo $manifestTitle; ?></title>

<link href="../../estilos/mk-auth.css" rel="stylesheet" type="text/css" />
<link href="../../estilos/font-awesome.css" rel="stylesheet" type="text/css" />
<link href="../../estilos/bi-icons.css" rel="stylesheet" type="text/css" />

<script src="../../scripts/jquery.js"></script>
<script src="../../scripts/mk-auth.js"></script>

</head>
<body>

<?php include('../../topo.php'); ?>

<nav class="breadcrumb has-bullet-separator is-centered" aria-label="breadcrumbs">
<ul>
<li><a href="#"> ADDON</a></li>
<li class="is-active"><a href="#" aria-current="page"> <?php echo htmlspecialchars($manifestTitle . " - V " . $manifestVersion); ?> </a></li>
</ul>
</nav>

<?php
// Configurações para acessar o Mikrotik
$mikrotik_ip = '192.168.88.1'; // Substitua pelo Ip Servidor
$mikrotik_username = 'Usuario'; // Substitua pelo Usuario real
$mikrotik_password = 'Senha'; // Substitua pela senha real

// URL da API REST do Mikrotik
$api_url = "https://$mikrotik_ip/rest/system/resource";

// Inicia a sessão cURL
$ch = curl_init();

// Configura as opções da requisição cURL
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Desabilita a verificação SSL (não recomendado em produção)
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, "$mikrotik_username:$mikrotik_password");

// Executa a requisição cURL
$response = curl_exec($ch);

// Verifica se ocorreu algum erro durante a requisição
if(curl_errno($ch)){
    $error_msg = 'Erro ao acessar a API do Mikrotik: ' . curl_error($ch);
} else {
    // Decodifica o JSON de resposta
    $data = json_decode($response, true);
}

// Fecha a sessão cURL
curl_close($ch);

?>

<div class="container">
    <h2>Informações do Sistema Mikrotik</h2>
    <?php
    if (isset($error_msg)) {
        echo '<p>' . $error_msg . '</p>';
    } else {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
    ?>
</div>

<?php include('../../baixo.php'); ?>

<script src="../../menu.js.hhvm"></script>

</body>
</html>
