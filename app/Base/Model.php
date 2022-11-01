<?php
namespace FarhangNegari\App\Base;

use FarhangNegari\App\Base\DB\Mysqli;

class Model extends Mysqli
{
    public function __construct()
    {
        parent::__construct(config('database'));
    }
}
?>