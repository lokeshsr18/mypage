<?php
session_start();
require_once 'blockchain.php';

if (!isset($_SESSION['blockchain'])) {
    $_SESSION['blockchain'] = new Blockchain();
}

$blockchain = $_SESSION['blockchain'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'sendRequest') {
    $ipAddress = $_SERVER['REMOTE_ADDR'];

    if ($blockchain->isValidRequest($ipAddress)) {
        $blockchain->addBlock("Request from $ipAddress");
        echo "Request accepted. Block added to the chain.";
    } else {
        echo "Request rejected (potential DDoS).";
    }
}

$_SESSION['blockchain'] = $blockchain;
