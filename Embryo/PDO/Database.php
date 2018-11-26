<?php 

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
    }