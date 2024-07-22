<?php
header('Content-Type: application/json');

$ipAddress = $_POST['ip_address'] ?? '';
$command = $_POST['command'] ?? '';
$position = $_POST['position'] ?? '';
$color = $_POST['color'] ?? '';

function send_command($command, $ipAddress, $position = null) {
    $port = 2223;
    $message = $position !== null ? "$command $position 20\r\n" : "$command\r\n";
    $response = ['success' => false, 'message' => ''];

    try {
        $sock = fsockopen($ipAddress, $port, $errno, $errstr, 10);
        if ($sock) {
            fwrite($sock, $message);
            fclose($sock);
            $response['success'] = true;
            $response['message'] = "Sent command: $message to $ipAddress";
        } else {
            $response['message'] = "Failed to send command: $errstr";
        }
    } catch (Exception $e) {
        $response['message'] = "Exception: " . $e->getMessage();
    }

    return $response;
}

function send_color_message($color, $ipAddress, $position) {
    $port = 2223;
    $message = "LIGHT_SET $position $color";
    return send_command($message, $ipAddress);
}

if ($command) {
    $result = send_command($command, $ipAddress, $position);
    echo json_encode($result);
    exit;
}

if ($color) {
    $result = send_color_message($color, $ipAddress, $position);
    echo json_encode($result);
    exit;
}

echo json_encode(['success' => false, 'message' => 'No valid parameters']);
?>
