<?php

namespace FarhangNegari\App\Base\DB;
use Exception;
// use FarhangNegari\App\Base\Interface\ModelInterface;
use ReflectionClass;

class Mysqli
{
    protected static $_instance;

    protected $_query;

    public $_tableName = '';
    
    private $prefix = '';
    
    public $pageLimit = 50;
    public $page = 1;

    public $totalPages = 0;
    private $_where = 1;

    public $defConnectionName = 'default';

    private $_mysqli = [];

    protected $connectionsSettings = array();

    protected $transaction_in_progress = false;

    public function __construct($host = null, $username = null, $password = null, $databaseName = null, $port = '3306', $charset = 'utf8', $socket = null)
    {
        if (is_array($host)) {
            foreach ($host as $key => $val) {
                $$key = $val;
            }
        }

        $this->addConnection('default', array(
            'host' => $host,
            'username' => $username,
            'password' => $password,
            'databaseName' => $databaseName,
            'port' => $port,
            'socket' => $socket,
            'charset' => $charset
        ));
        
        if (isset($prefix)) {
            $this->setPrefix($prefix);
        }
        self::$_instance = $this;
    }

    public function addConnection($name, array $params)
    {
        $this->connectionsSettings[$name] = array();
        foreach (array('host', 'username', 'password', 'databaseName', 'port', 'socket', 'charset') as $k) {
            $prm = isset($params[$k]) ? $params[$k] : null;

            if ($k == 'host') {
                if (is_object($prm))
                    $this->_mysqli[$name] = $prm;

                if (!is_string($prm))
                    $prm = null;
            }
            $this->connectionsSettings[$name][$k] = $prm;
        }
        
        return $this;
    }

    public function setPrefix($prefix = '')
    {
        self::$prefix = $prefix;
        return $this;
    }

    public function getPrefix()
    {
        return self::$prefix;
    }

    public function connect($connectionName = 'default')
    {
        if(!isset($this->connectionsSettings[$connectionName]))
            throw new Exception('Connection profile not set');

        $pro = $this->connectionsSettings[$connectionName];
        $params = array_values($pro);
        $charset = array_pop($params);
        
        if (empty($pro['host']) && empty($pro['socket'])) {
            throw new Exception('MySQL host or socket is not set');
        }

        $mysqlic = new ReflectionClass('mysqli');
        $mysqli = $mysqlic->newInstanceArgs($params);

        if ($mysqli->connect_error) {
            throw new Exception('Connect Error ' . $mysqli->connect_errno . ': ' . $mysqli->connect_error, $mysqli->connect_errno);
        }

        if (!empty($charset)) {
            $mysqli->set_charset($charset);
        }

        $this->_mysqli[$connectionName] = $mysqli;
    }

    public function connection($name = 'default')
    {
        if (!isset($this->connectionsSettings[$name]))
            throw new Exception('Connection ' . $name . ' was not added.');

        $this->defConnectionName = $name;
        return $this;
    }

    public function disconnect($connection = 'default')
    {
        if (!isset($this->_mysqli[$connection]))
            return;

        $this->_mysqli[$connection]->close();
        unset($this->_mysqli[$connection]);
    }

    public static function getInstance()
    {
        return self::$_instance;
    }

    protected function reset()
    {
        $this->_where = array();
        $this->returnType = 'array';
        $this->_lastInsertId = null;

        return $this;
    }

    public function mysqli()
    {
        if (!isset($this->_mysqli[$this->defConnectionName])) {
            $this->connect($this->defConnectionName);
        }
        return $this->_mysqli[$this->defConnectionName];
    }

    public function find($id)
    {
        $result =  $this->mysqli()->query(
            "SELECT * FROM `$this->_tableName` where `id` = '$id' LIMIT 1"
        );
        
        return $result ? $result->fetch_assoc() : [];
    }

    public function setTableName($name){
        $this->_tableName = $name;
        return $this;
    }

    public function byId($id)
    {
        return $this->find($id);
    }

    public function where($where)
    {
        $this->_where = $where;
        return $this;
    }

    public function get($where = null)
    {
        if($where != null)
            $this->_where = $where;
            
        $result =  $this->mysqli()->query(
            "SELECT * FROM `$this->_tableName` where $this->_where"
        );
        
        return $result->fetch_all();
    }

    public function delete($where = null)
    {
        if($where != null)
            $this->_where = $where;
        return $this->mysqli()->query(
            "DELETE FROM `$this->_tableName` where $this->_where"
        );
    }

    public function insert($data)
    {
        $keys = '`'.implode('`,`', array_keys($data)).'`';
        $values = '\''.implode('\',\'', array_values($data)).'\'';
        
        $check = $this->mysqli()->query(
            "INSERT INTO `$this->_tableName` ($keys) VALUES ($values)"
        );
        
        $this->reset();
        return $check ? $this->mysqli()->insert_id : null;
    }

    public function update($data)
    {
        $keys = array_keys($data);
        $values = array_values($data);

        $data = '';
        foreach($keys as $key=>$val)
        {
            $data .= " `$val` = '$values[$key]' ,";
        }
        $data = rtrim( $data , ',');
        
        $check = $this->mysqli()->query(
            "UPDATE `$this->_tableName` SET $data where $this->_where"
        );
        $this->reset();
        return $this->mysqli()->affected_rows;
    }

    public function paginate($request)
    {
        $page = $request->get('page',$this->page);
        -- $page;
        $this->pageLimit = $request->get('perpage' , $this->pageLimit);

        $total =  $this->mysqli()->query(
            "SELECT * FROM `$this->_tableName` where $this->_where"
        );
        
        $count = $total->fetch_all();
        if(count($count) > ($page * $this->pageLimit) )
        { 
            $result =  $this->mysqli()->query(
                "SELECT * FROM `$this->_tableName` where $this->_where limit $page , $this->pageLimit"
            );
            
            while($row = $result->fetch_assoc()){
                $data[] = $row;
            }
        }else{
            $data = [];
        }

        $this->reset();
        return ['data'=>$data , 'page'=>$this->page , 'perpage'=>$this->pageLimit , 'count'=>count($count ? $count : []) ];
    }

    public function first()
    {
        $result =  $this->mysqli()->query(
            "SELECT * FROM `$this->_tableName` where $this->_where"
        );
        
        return $result ? $result->fetch_assoc()  : null;
    }

    public function bginTransaction()
    {
        $this->mysqli()->autocommit(FALSE);
        return $this;
    }

    public function commit()
    {
        if (!$this->mysqli()->commit()) {
            throw new Exception($this->mysqli()->errno , 500);
        }
        $this->mysqli()->autocommit(TRUE);
        return true;
    }

    public function rollback()
    {
        $this->mysqli()->rollback();
        $this->mysqli()->autocommit(TRUE);
        return true;
    }
}
?>