<?php

/**
 * Default configuration
 */

$cfg = new stdClass();

$cfg->environment = 'dev';

// Site
$cfg->debug = false;
$cfg->root_url = '/';
$cfg->upload_url = $cfg->root_url . '/upload/';

// Directories
$cfg->tmp_dir = '/tmp';
$cfg->upload_dir = ROOT . '/www/upload';

// Database
$cfg->db = new stdClass();
$cfg->db->host = 'localhost';
$cfg->db->user = 'user';
$cfg->db->password = 'password';
$cfg->db->dbname = 'database';

// Memcache
$cfg->memcache_timeout = 20;
$cfg->memcache_host = 'localhost';
$cfg->memcache_port = 11211;
$cfg->memcache_active = true;
