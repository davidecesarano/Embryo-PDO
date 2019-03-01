<?php 

    /**
     * Database
     * 
     * This class set PDO connection and actives query builder
     * from table or query.
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link https://github.com/davidecesarano/embryo-pdo
     */
    
    namespace Embryo\PDO;

    use Embryo\PDO\QueryBuilder\QueryBuilder;
    use Embryo\PDO\QueryBuilder\Query;

    class Database
    {
        /**
         * @var PDO $pdo
         */
        private $pdo;

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
         * @param string $database
         * @return self
         */
        public function connection(string $database = 'local'): self
        {
            if (!isset($this->database[$database])) {
                throw new \InvalidArgumentException("Database $database doesn't exists.");
            }

            try {

                $database = $this->database[$database];
                $engine   = $database['engine']; 
                $host     = $database['host'];
                $name     = $database['name'];
                $user     = $database['user']; 
                $password = $database['password'];
                $charset  = $database['charset'];
                $options  = $database['options'];
                $dsn      = $engine.':dbname='.$name.";host=".$host.";charset=".$charset;
                
                $this->pdo = new \PDO($dsn, $user, $password, $options);
                return $this;

            } catch (\PDOException $e) {
                throw new \PDOException($e->getMessage());
            }
        }

        /**
         * Set query builder from table.
         *
         * @param string $table
         * @return QueryBuilder
         */
        public function table(string $table): QueryBuilder
        {
            return new QueryBuilder($this->pdo, $table);
        }

        /**
         * Set query from string
         *
         * @param string $query
         * @return Query
         */
        public function query(string $query): Query
        {
            return (new Query($this->pdo))->query($query);
        }

        /**
         * Transaction
         * 
         * @param Closure $callback
         * @return mixed
         */
        public function transaction(\Closure $callback)
        {
            $callback = \Closure::bind($callback, $this);
            try {

                $this->pdo->beginTransaction();
                $result = $callback();
                $this->pdo->commit();
                return $result;

            } catch (\PDOException $e) {
                
                $this->pdo->rollback();
                throw new \PDOException($e->getMessage());
                
            }
        }
    }