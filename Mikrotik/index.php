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

<style>
    /* Estilos CSS personalizados */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #ffffff;
        margin: 0;
        padding: 0;
        color: #333;
    }

    .container {
        width: 100%;
        max-width: 1600px; /* Aumentando a largura máxima */
        margin: 20px auto;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }

    h2 {
        margin-top: 0;
        text-align: center;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    tr:hover {
        background-color: #f9f9f9;
    }
</style>

<script src="../../scripts/jquery.js"></script>
<script src="../../scripts/mk-auth.js"></script
</head>
<body>

<?php include('../../topo.php'); ?>

<nav class="breadcrumb has-bullet-separator is-centered" aria-label="breadcrumbs">
<ul>
<li><a href="#"> ADDON</a></li>
<li class="is-active"><a href="#" aria-current="page"> <?php echo htmlspecialchars($manifestTitle . " - V " . $manifestVersion); ?> </a></li>
</ul>
</nav>

<div class="container">

<h2 style="color: blue; text-align: center; font-weight: bold;">API Mikrotik PPPOES</h2>
	
<?php
// Configurações para acessar o Mikrotik
$mikrotik_ip = '192.168.88.1'; // Ip do seu Servidor
$mikrotik_username = 'Usuario'; // Substitua pelo usuario real
$mikrotik_password = 'Senha'; // Substitua pela senha real

$user_number = '100'; // Definindo a variável para o valor "100"

// URL da API REST do Mikrotik para obter os ativos PPPoE filtrados pelo nome "100" e retornar apenas as propriedades especificadas
$api_url_ppp_active = "https://$mikrotik_ip/rest/ppp/active?name=$user_number&.proplist=name,address,caller-id,uptime";

// URL da API REST do Mikrotik para obter informações do serviço PPPoE do servidor e retornar apenas as propriedades especificadas
$api_url_pppoe_service = "https://$mikrotik_ip/rest/interface/pppoe-server?user=$user_number&.proplist=service,remote-address";

// Função para fazer a requisição cURL
function get_mikrotik_data($url, $username, $password) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Desabilita a verificação SSL (não recomendado em produção)
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    $response = curl_exec($ch);
    if(curl_errno($ch)){
        $error_msg = 'Erro ao acessar a API do Mikrotik: ' . curl_error($ch);
        curl_close($ch);
        return ['error' => $error_msg];
    }
    curl_close($ch);
    return json_decode($response, true);
}

// Obtém dados dos ativos PPPoE
$ppp_active_data = get_mikrotik_data($api_url_ppp_active, $mikrotik_username, $mikrotik_password);

// Obtém informações do serviço PPPoE do servidor
$pppoe_service_data = get_mikrotik_data($api_url_pppoe_service, $mikrotik_username, $mikrotik_password);
?>

<?php
if (isset($ppp_active_data['error'])) {
    echo '<p>' . $ppp_active_data['error'] . '</p>';
} else {
    if (empty($ppp_active_data)) {
        echo "<p style='font-weight: bold; color: green; text-align: center;'>Nenhum PPPoE encontrado com o nome $user_number.</p>";
    } else {
        $contador = 0; // Inicializa o contador
        // Exibe o número total de PPPoEs encontrados no topo da tabela
        echo '<p style="font-weight: bold; color: green; text-align: center;">Total de PPPoEs encontrados: ' . count($ppp_active_data) . '</p>';
        echo '<table>';
        echo '<tr><th>Nome</th>
              <th>Serviço</th>
              <th>MAC</th>
              <th>IP</th>
              <th>Porta</th>
              <th>Up-Time</th>
              </tr>';

        foreach ($ppp_active_data as $item) {
            ++$contador; // Incrementa o contador a cada iteração
            echo '<tr>';
            // Adiciona a coluna do nome com negrito e cor azul
            echo '<td style="color: blue; font-weight: bold;">' . $item['name'] . '</td>';

            // Encontra o serviço correspondente com base no remote-address (caller-id)
            $service = '';
            foreach ($pppoe_service_data as $service_item) {
                if ($service_item['remote-address'] == $item['caller-id']) {
                    $service = $service_item['service'];
                    break;
                }
            }

            echo '<td><b>' . $service . '</b></td>'; // Adiciona a coluna do serviço PPPoE com negrito

            // Adiciona a coluna do caller-id com negrito e cor azul
            echo '<td style="color: blue; font-weight: bold;">' . $item['caller-id'] . '</td>';

            // Adiciona a coluna do address com negrito e cor azul
            echo '<td><a href="http://' . $item['address'] . '" target="_blank" style="color: blue; font-weight: bold;">' . $item['address'] . '</a></td>';

            // Adiciona a coluna do 8888 com negrito e cor azul
            echo '<td><a href="http://' . $item['address'] . ':8888" target="_blank" style="color: blue; font-weight: bold;">8888</a></td>';

            // Adiciona a coluna do uptime com negrito e cor azul
            echo '<td style="color: blue; font-weight: bold;">' . $item['uptime'] . '</td>';

            echo '</tr>';
        }
        echo '</table>';
    }
}
?>
</div>

<?php include('../../baixo.php'); ?>

<script src="../../menu.js.hhvm"></script>

</body>
</html>
