<?php 

    /**
     * Database
     * 
     * This class set PDO connection.
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link https://github.com/davidecesarano/embryo-pdo
     */
    
    namespace Embryo\PDO;

    use Embryo\PDO\Connection;

    class Database
    {
        /**
         * @var array $connections
         */
        private $connections = [];

        /**
         * @var array $database
         */
        private $database;

        /**
         * Set database array.
         *
         * @param array $database
         */
        public function __construct(array $database)
        {
            $this->database = $database;
        }

        /**
         * Set PDO connection from database array.
         *
         * @param string $name
         * @return Connection
         */
        public function connection(string $name = 'local'): Connection
        {
            if (!isset($this->database[$name])) {
                throw new \InvalidArgumentException("Database $database doesn't exists.");
            }

            try {

                $database = $this->database[$name];
                $engine   = $database['engine']; 
                $host     = $database['host'];
                $name     = $database['name'];
                $user     = $database['user']; 
                $password = $database['password'];
                $charset  = $database['charset'];
                $options  = $database['options'];
                $dsn      = $engine.':dbname='.$name.";host=".$host.";charset=".$charset;
                
                if (array_key_exists($name, $this->connections)) {
                    return $this->connections[$name];
                }

                $pdo = new \PDO($dsn, $user, $password, $options);
                $connection = new Connection($pdo);
                $this->connections[$name] = $connection;
                return $connection;
                
            } catch (\PDOException $e) {
                throw new \PDOException($e->getMessage());
            }
        }
    }