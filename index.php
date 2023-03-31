<?php

// Cargar variables de entorno desde el archivo .env
$env = parse_ini_file('.env');

// Definir variables constantes
$OPENAI_API_KEY = $env['OPENAI_API_KEY'];
$ALLOWED_CHAT_IDS = explode(',', $env['ALLOWED_CHAT_IDS']); 
$ADMIN_CHAT_ID = $env['ADMIN_CHAT_ID'];

// Definir variables constantes de Telegram
$TELEGRAM_TOKEN = $env['TELEGRAM_BOT_TOKEN'];
$TELEGRAM_API = "https://api.telegram.org/bot" . $TELEGRAM_TOKEN;
$input = file_get_contents("php://input");
$update = json_decode($input, true);
$CHAT_ID = $update['message']['chat']['id'];
$NAME = $update['message']['from']['first_name'];
$MESSAGE = $update['message']['text'];

// ------------------ Funciones para las conversaciones ------------------
// Cargar el archivo JSON de conversaciones previas correspondiente al chat
function saveConversations($chatId, $conversations) {
    // Guardar las conversaciones previas en el archivo JSON correspondiente al chat
    $filename = $chatId . ".json";
    file_put_contents($filename, json_encode($conversations));
}

// Cargar el archivo JSON de conversaciones previas correspondiente al chat
function getConversations($chatId) {
    $filename = $chatId . ".json";
    if (file_exists($filename)) {
        $conversations = json_decode(file_get_contents($filename), true);
    } else {
        $conversations = array();
    }
    return $conversations;
}

// Eliminar el archivo JSON de conversaciones previas correspondiente al chat guardandolas en un archivo de respaldo
function deleteConversations($chatId) {
    $filename = $chatId . ".json";
    $hash = md5(json_encode(getConversations($chatId)));
    if (!file_exists('bak')) {
        mkdir('bak');
    }
    
    // Guardar las conversaciones previas en el archivo JSON correspondiente al chat con el hash y la fecha
    rename($filename, 'bak/'. date("Y-m-d") .'-'. $filename . "." . $hash . ".bak.json");
}

// ------------------ Funciones para Telegram ------------------
function checkAdmin($chatId) {
    if ($chatId == $GLOBALS['ADMIN_CHAT_ID']) {
        return true;
    } else {
        return false;
    }
}

function sendMessage($chatId, $response){
    $response = preg_replace('/```(.+?)```/', '<b>$1</b>', $response);
    error_log("response:");
    error_log($response);
    $response = htmlspecialchars($response);
    
    // https://core.telegram.org/bots/api#sendmessage
    $url = $GLOBALS['TELEGRAM_API'] . '/sendMessage?chat_id=' . $chatId . '&parse_mode=HTML&text=' . urlencode($response) ;
    error_log($url);

    file_get_contents($url);
}

function setChatEnabled($chatId, $enabled) {
    $enabled_chats = json_decode(file_get_contents('enabled_chats.json'), true);
    $enabled_chats[$chatId] = $enabled;
    file_put_contents('enabled_chats.json', json_encode($enabled_chats));
}

function getChatEnabled($chatId) {
    $enabled_chats = json_decode(file_get_contents('enabled_chats.json'), true);
    error_log("enabled_chats:");
    error_log(json_encode($enabled_chats));

    if (isset($enabled_chats[$chatId])) {
        error_log("enabled_chats[$chatId]:");
        error_log($enabled_chats[$chatId]);
        return $enabled_chats[$chatId];
    } else {
        return false;
    }
}

// ------------------ Funciones para OpenAI ------------------.
function chatGPT3Turbo($apiKey, $chatId, $message){ 
    global $OPENAI_API_KEY;   

    $previousMessages = getConversations($chatId);

    $data = array(
        "model" => "gpt-3.5-turbo",
        "messages" => $previousMessages
    );

    // Añadir nuevo mensaje
    $data['messages'][] = array("role" => "user", "content" => "$message");

    // $api_url = "https://api.openai.com/v1/engines/davinci/completions";
    $api_url = "https://api.openai.com/v1/chat/completions";

    // Codificamos los datos en formato JSON
    $json_data = json_encode($data);

    // Configuramos la petición CURL para consultar la API de OpenAI
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "Authorization: Bearer " . $OPENAI_API_KEY
    ));

    // Ejecutamos la petición CURL
    $response = curl_exec($ch);

    // Cerramos la conexión CURL
    curl_close($ch);

    // Procesamos la respuesta JSON
    $result = json_decode($response, true); 

    // Mostrar el array de result con sus datos
    if(curl_errno($ch)){
        error_log('Curl error: ' . curl_error($ch));
    }

    $chatResponse = $result['choices'][0]['message']['content'];
    $conversations = $data['messages'];
    $conversations[] = array("role" => "assistant", "content" => $chatResponse);
    saveConversations($chatId, $conversations);

    return $chatResponse;
}

function start($chatId){
    $response = "Hola ". $GLOBALS["NAME"] .", soy un bot de Telegram que usa OpenAI GPT-3 para responder a tus mensajes. Puedes usar el comando /help para ver los comandos disponibles.";
    sendMessage($chatId, $response);
}

function addChatIdAccess($chatId, $newChatId){
    if(!checkAdmin($chatId)){
        $response = "No tienes permisos para ejecutar este comando.";
        sendMessage($chatId, $response);
        return;
    }

    setChatEnabled($newChatId, true);
    $response = "El chat $newChatId ha sido añadido a la lista de chats habilitados.";
    sendMessage($chatId, $response);
    $response = "Tu acceso especial al chat ha sido aprobado por el administrador.";
    sendMessage($newChatId, $response);
}

function removeChatIdAccess($chatId, $newChatId){
    if(!checkAdmin($chatId)){
        $response = "No tienes permisos para ejecutar este comando.";
        sendMessage($chatId, $response);
        return;
    }

    setChatEnabled($newChatId, false);
    $response = "El chat $newChatId ha sido deshabilitado de la lista de chats habilitados.";
    sendMessage($chatId, $response);
    $response = "Tu acceso especial al chat ha sido eliminado por el administrador.";
    sendMessage($newChatId, $response);
}

// Enable global chats
function enableGlobalChats($chatId){
    if(!checkAdmin($chatId)){
        $response = "No tienes permisos para ejecutar este comando.";
        sendMessage($chatId, $response);
        return;
    }
    setChatEnabled($chatId, true);
    $response = "Todos los chats habilitados.";
    sendMessage($chatId, $response);
}

// Disable global chats
function disableGlobalChats($chatId){
    if(!checkAdmin($chatId)){
        $response = "No tienes permisos para ejecutar este comando.";
        sendMessage($chatId, $response);
        return;
    }
    setChatEnabled($chatId, false);
    $response = "Todos los chats deshabilitados.";
    sendMessage($chatId, $response);
}

// Obtener tu ID de chat
function getChatId($chatId){
    $response = "Tu ID de chat es: $chatId";
    sendMessage($chatId, $response);
}

// Limpiar lista de conversaciones
function clearConversations($chatId){
    $response = "Lista de conversaciones limpiada.";
    sendMessage($chatId, $response);
    deleteConversations($chatId);
}

function responseGPT($chatId, $message, $is_chat_enabled){
    global $ALLOWED_CHAT_IDS;

    $enabled_chats = getChatEnabled($chatId);

    if($chatId == $GLOBALS['ADMIN_CHAT_ID'] || 
        (isset($ALLOWED_CHAT_IDS["$chatId"]) && $ALLOWED_CHAT_IDS["$chatId"]) || 
        (isset($enabled_chats["0"]) && $enabled_chats["0"]) || 
        $is_chat_enabled) 
    {

        $response = chatGPT3Turbo($GLOBALS['OPENAI_API_KEY'], "$chatId", $message);
        sendMessage($chatId, $response);
    }else{
        error_log("El chat $chatId no tiene acceso al bot. Mensaje: $message");

        $response = 'El chat no se encuentra disponible ahora.';
        sendMessage($chatId, $response);
    }
}

function help($chatId){
    $response = "Comandos disponibles: \n";
    $response .= "/start - Inicia el bot. \n";
    $response .= "/enable_global_chats - Habilita el uso del bot en todos los chats. \n";
    $response .= "/disable_global_chats - Deshabilita el uso del bot en todos los chats. \n";
    $response .= "/get_chat_id - Obtiene el ID de chat. \n";
    $response .= "/clear_conversations - Limpia la lista de conversaciones. \n";
    $response .= "/add [chat_id] - Añade un chat a la lista de chats habilitados. \n";
    $response .= "/delete [chat_id] - Elimina un chat de la lista de chats habilitados. \n";
    $response .= "/help - Muestra esta ayuda. \n";


    sendMessage($chatId, $response);
}


function main($chatId, $message){
    // Comprobar si el chat está habilitado o no para usar el bot de OpenAI
    if (!file_exists('enabled_chats.json')) {
        setChatEnabled(0, false);
    }
    $is_chat_enabled = getChatEnabled($chatId);


    // Comprueba si el mensaje empieza por /add y le pasa el siguiente dato.
    if (strpos($message, '/add') === 0) { addChatIdAccess($chatId, (int) substr($message, 5)); return;} 
    // Comprueba si el mensaje empieza por /delete
    if (strpos($message, '/delete') === 0) { removeChatIdAccess($chatId, (int)substr($message, 8)); return;}

    switch($message) {
        case '/start': start($chatId); break;
        case '/enable_global_chats': enableGlobalChats($chatId); break;
        case '/disable_global_chats': disableGlobalChats($chatId); break;
        case '/get_chat_id': getChatId($chatId); break;
        case '/clear_conversations': clearConversations($chatId); break;
        case '/help': help($chatId); break;
        default: responseGPT($chatId, $message, $is_chat_enabled); break;
    }
}


main( $CHAT_ID, $MESSAGE );



