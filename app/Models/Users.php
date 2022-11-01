<?php
namespace FarhangNegari\App\Models;

use FarhangNegari\App\Base\Model;

class Users extends Model
{
    public $_tableName = 'users';
    public function __constract()
    {
        parent::setTableName($this->_tableName);
    }
}
?>