<?php
class Manager {
    private $btcAddr;
    private $db;

    function __construct($btcAddress) {
//        $mongo = new MongoClient();
        $mongo = new MongoClient('mongodb://admin:W-blx9dMT3xk@5550e877e0b8cd8cfa00016a-ssttevee.rhcloud.com:61276/');
        $this->db = $mongo->btcfaucet;
        $this->btcAddr = $btcAddress;
    }

    private function createAccount() {
        $newUser = array(
            "address" => $this->btcAddr,
            "created" => time(),
            "satbalance" => 0,
            "alltimebal" => 0,
            "satspent" => 0,
            "satwithdrawn" => 0,
        );
        if($this->db->users->insert($newUser)) return $newUser;
        die("failed to add new user");
    }

    function getBalance() {
        $cursor = $this->db->users->findOne(array( 'address' => $this->btcAddr ), array('satbalance'));

        if($cursor == null) {
            $this->createAccount();
            return $this->getBalance();
        } else {
            return $cursor['satbalance'];
        }
    }

    function getAddress() {
        return $this->btcAddr;
    }
}