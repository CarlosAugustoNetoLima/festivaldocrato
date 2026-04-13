<?php

namespace App\Models;

use App\Config\Database;

class BaseModel
{
    protected $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }
}
