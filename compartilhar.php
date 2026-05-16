<?php
// compartilhar.php - Página dedicada para compartilhamento com meta tags dinâmicas
require_once 'firebase-php-sdk/vendor/autoload.php'; // Se você não tem o SDK PHP do Firebase, usaremos uma abordagem mais simples: consultar o Firestore via REST.

// Como a maioria das hospedagens não tem o SDK PHP do Firebase, vou usar a API REST do Firestore
// Você precisa informar sua chave de API do Firebase (mesma do front-end)
$firebaseApiKey = "AIzaSyDNsPEpaYJ_RfMTXQqPxb3A-wu1sb5UClc";
$projectId = "ofertasdapoliana";

function getProduct($id) {
    global $projectId;
    $url = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/products/{$id}";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    if ($response) {
        $data = json_decode($response, true);
        if (isset($data['fields'])) {
            $fields = $data['fields'];
            $product = [];
            foreach ($fields as $key => $value) {
                if (isset($value['stringValue'])) $product[$key] = $value['stringValue'];
                if (isset($value['integerValue'])) $product[$key] = $value['integerValue'];
                if (isset($value['doubleValue'])) $product[$key] = $value['doubleValue'];
            }
            return $product;
        }
    }
    return null;
}

function getConfig() {
    global $projectId;
    $url = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/config/site";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    if ($response) {
        $data = json_decode($response, true);
        if (isset($data['fields'])) {
            $fields = $data['fields'];
            $config = [];
            foreach ($fields as $key => $value) {
                if (isset($value['stringValue'])) $config[$key] = $value['stringValue'];
            }
            return $config;
        }
    }
    return [];
}

$productId = isset($_GET['id']) ? $_GET['id'] : '';
$product = null;
$config = getConfig();
$defaultOgImage = isset($config['ogImage']) ? $config['ogImage'] : 'https://i.ibb.co/Fk3bdXRb/logo.png';
$defaultOgTitle = isset($config['ogTitle']) ? $config['ogTitle'] : 'Dicas da Poli | Seu guia de compras online';
$defaultOgDesc = isset($config['ogDescription']) ? $config['ogDescription'] : 'As melhores ofertas em moda, eletrônicos e muito mais!';

if ($productId) {
    $product = getProduct($productId);
}

if ($product) {
    $title = $product['title'] ?? $defaultOgTitle;
    $desc = isset($product['description']) ? substr($product['description'], 0, 150) : $defaultOgDesc;
    $image = $product['imageUrl'] ?? $defaultOgImage;
    $urlLink = "https://dicasdapoli.com.br/?produto={$productId}";
} else {
    $title = $defaultOgTitle;
    $desc = $defaultOgDesc;
    $image = $defaultOgImage;
    $urlLink = "https://dicasdapoli.com.br/";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <meta property="og:title" content="<?php echo htmlspecialchars($title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($desc); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($image); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($urlLink); ?>">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <meta http-equiv="refresh" content="0; url=<?php echo htmlspecialchars($urlLink); ?>">
</head>
<body>
    <p>Redirecionando... <a href="<?php echo htmlspecialchars($urlLink); ?>">Clique aqui</a></p>
</body>
</html>