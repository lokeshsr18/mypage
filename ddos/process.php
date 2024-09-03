<?php
session_start();

class Block {
    public $index;
    public $timestamp;
    public $data;
    public $previousHash;
    public $hash;

    public function __construct($index, $timestamp, $data, $previousHash = '') {
        $this->index = $index;
        $this->timestamp = $timestamp;
        $this->data = $data;
        $this->previousHash = $previousHash;
        $this->hash = $this->calculateHash();
    }

    public function calculateHash() {
        return hash('sha256', $this->index . $this->timestamp . $this->data . $this->previousHash);
    }
}

class Blockchain {
    public $chain;
    public $requestCount;

    public function __construct() {
        $this->chain = [$this->createGenesisBlock()];
        $this->requestCount = [];
    }

    private function createGenesisBlock() {
        return new Block(0, time(), "Genesis Block", "0");
    }

    public function getLatestBlock() {
        return $this->chain[count($this->chain) - 1];
    }

    public function addBlock($data) {
        $latestBlock = $this->getLatestBlock();
        $newBlock = new Block(count($this->chain), time(), $data, $latestBlock->hash);
        $this->chain[] = $newBlock;
    }

    public function isValidRequest($ipAddress) {
        $currentTime = time();
        if (!isset($this->requestCount[$ipAddress])) {
            $this->requestCount[$ipAddress] = [[$currentTime, 1]];
            return true;
        }

        $this->requestCount[$ipAddress] = array_filter($this->requestCount[$ipAddress], function($request) use ($currentTime) {
            return $currentTime - $request[0] <= 60;
        });

        $totalRequests = array_sum(array_column($this->requestCount[$ipAddress], 1));

        if ($totalRequests >= 100) {
            return false;
        }

        $this->requestCount[$ipAddress][] = [$currentTime, 1];
        return true;
    }

    public function getChainData() {
        return array_map(function($block) {
            return [
                'index' => $block->index,
                'timestamp' => $block->timestamp,
                'data' => $block->data,
                'hash' => $block->hash
            ];
        }, $this->chain);
    }
}

if (!isset($_SESSION['blockchain'])) {
    $_SESSION['blockchain'] = new Blockchain();
}

$blockchain = $_SESSION['blockchain'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'sendRequest') {
    $ipAddress = $_SERVER['REMOTE_ADDR'];

    if ($blockchain->isValidRequest($ipAddress)) {
        $blockchain->addBlock("Request from $ipAddress");
        $response = [
            'status' => 'success',
            'message' => "Request accepted. Block added to the chain.",
            'chainData' => $blockchain->getChainData()
        ];
    } else {
        $response = [
            'status' => 'error',
            'message' => "Request rejected (potential DDoS).",
            'chainData' => $blockchain->getChainData()
        ];
    }

    echo json_encode($response);
}

$_SESSION['blockchain'] = $blockchain;
