<?php 

    /**
     * Query
     * 
     * This class performs a query.
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link https://github.com/davidecesarano/embryo-pdo
     */

    namespace Embryo\PDO\QueryBuilder;

    class Query 
    {
        /**
         * @var \PDO $pdo
         */
        private $pdo;
        
        /**
         * @var string $query
         */
        private $query;

        /**
         * @var \PDOStatement|bool $stmt
         */
        private $stmt;
        
        /**
         * @var array $values
         */
        private $values = [];
        
        /**
         * @var bool $execute
         */
        private $execute;

        /**
         * @var int $lastInsertId
         */
        private $lastInsertId = [];

        /**
         * Set PDO.
         *
         * @param \PDO $pdo
         */
        public function __construct(\PDO $pdo)
        {
            $this->pdo = $pdo;
        }

        /**
         * Set and prepare query.
         *
         * @param string $query
         * @return self
         */
        public function query(string $query): self
        {
            $this->query = $query;
            $this->stmt = $this->pdo->prepare($query);
            return $this;
        }

        /**
         * Set bind value.
         *
         * @param array $values
         * @return self
         */
        public function values(array $values): self
        {
            $this->values = $values;
            foreach ($values as $key => $value) {
                
                if (!is_array($value)) {
                    $this->stmt->bindValue(":$key", $value);
                } else {
                    
                    foreach($value as $k => $v){
                        $this->stmt->bindValue(":$k", $v);
                    }
                    
                }
                
            }
            return $this;
        }

        /**
         * Execute the preapre statement 
         * or emit PDO exception.
         *
         * @return void
         */
        public function execute()
        {
            try {
                $this->execute = $this->stmt->execute();
                $this->lastInsertId = $this->pdo->lastInsertId();

            } catch (\PDOException $e) {
                throw new \PDOException($e->getMessage());
            }
        }

        /**
         * Execute and return TRUE on success 
         * or FALSE on failure.
         *
         * @return bool
         */
        public function exec(): bool
        {
            $this->execute();
            return $this->execute;
        }

        /**
         * Execute and return last insert id.
         *
         * @return int
         */
        public function lastId(): int
        {
            $this->execute();
            return $this->lastInsertId;
        }

        /**
         * Execute and return row count.
         *
         * @return int
         */
        public function count(): int
        {
            $this->execute();
            return $this->stmt->rowCount();
        }

        /**
         * Execute and return a single
         * object row.
         *
         * @return object|bool
         */
        public function get()
        {
            $this->execute();
            return $this->stmt->fetch(\PDO::FETCH_OBJ);
        }

        /**
         * Execute and return an array
         * of objects.
         *
         * @return array|bool
         */
        public function all()
        {
            $this->execute();
            return $this->stmt->fetchAll(\PDO::FETCH_OBJ);
        }

        /**
         * Debug query.
         *
         * @return string
         */
        public function debug(): string
        {
            ob_start();
            $this->stmt->debugDumpParams();
            $output = ob_get_contents();
            ob_end_clean();
            return '<pre>'.htmlspecialchars($output).'</pre>';
        }
    }