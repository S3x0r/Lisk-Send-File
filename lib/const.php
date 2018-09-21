<?php

const DEBUG = false;
const LISK_PHP_VER = 1.0;
const LISK_START = 1464109200;
const USER_AGENT = "LISK-PHP ".LISK_PHP_VER." via CURL (Linux, en-GB)";
const SECURE = true;
const MAINNET = false;
//const NETWORK_HASH = "ed14889723f24ecc54871d058d98ce91ff2f973192075c0155ba2b7b70ad2511"; //mainnet
const NETWORK_HASH = "198f2b61a8eb95fbeed58b8216780b68f697f26b849acf00c8c93bb9b24f783d"; //community beta net
const MINVERSION = ">=1.0.0";
const OS = "lisk-php-api";
const API_VERSION = "1.0.0";
const BCONST = "api/node/constants";
const SEND_TRANSACTION_ENDPOINT = "api/transactions";
const NODE_STATUS = "api/node/status";
const ACCOUNTS = "api/accounts/";
const BLOCKS_ENDPOINT = "api/blocks/";
const VOTERS_ENDPOINT = "api/voters?publicKey=";
const VOTES_ENDPOINT = "api/votes?address=";
const DELEGATE_ENDPOINT = "api/delegates/";
const FORGING_ENDPOINT = "api/node/status/forging";
const DELEGATES_LIST_ENDPOINT = "api/delegates/";
const PENDING_TX_ENDPOINT = "api/node/transactions/unconfirmed";


const LSK_BASE = 100000000;
const SEND_FEE = 0.1 * LSK_BASE;
const DATA_FEE = 0.1 * LSK_BASE;
const SIG_FEE = 5 * LSK_BASE;
const DELEGATE_FEE = 25 * LSK_BASE;
const VOTE_FEE = 1 * LSK_BASE;
const MULTISIG_FEE = 5 * LSK_BASE;
const DAPP_FEE = 25 * LSK_BASE;


const SEND_TRANSACTION_FLAG = 0;
const SECOND_SIG_TRANSACTION_FLAG = 1;
const DELEGATE_TRANSACTION_FLAG = 2;
const VOTE_TRANSACTION_FLAG = 3;
const MULTISIG_TRANSACTION_FLAG = 4;
const DAPP_TRANSACTION_FLAG = 5;
const DAPP_IN_TRANSACTION_FLAG = 6;
const DAPP_OUT_TRANSACTION_FLAG = 7;

const PASSPHRASE_ENCRYPTION_VERSION = 1;
const PASSPHRASE_ENCRYPTION_ITERATIONS_DEFAULT = 1000000; //default value if not provided by user
?>
