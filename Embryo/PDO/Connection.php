<?php 

    /**
     * Database
     * 
     * This class actives query builder from table or query.
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link https://github.com/davidecesarano/embryo-pdo
     */

    namespace Embryo\PDO;

    use Embryo\PDO\QueryBuilder\QueryBuilder;
    use Embryo\PDO\QueryBuilder\Query;

    class Connection 
    {
        /**
         * @var \PDO $pdo
         */
        private $pdo;
        
        /**
         * Set PDO connection.
         *
         * @param \PDO $pdo
         */
        public function __construct(\PDO $pdo)
        {
            $this->pdo = $pdo;
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