<?php 

    /**
     * QueryBuilder
     * 
     * This query builder provides a convenient, fluent 
     * interface to creating and running database queries.
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link https://github.com/davidecesarano/embryo-pdo
     */

    namespace Embryo\PDO\QueryBuilder;
    
    use Embryo\PDO\QueryBuilder\Query;
    
    class QueryBuilder
    {
        /**
         * @var PDO $pdo
         */
        private $pdo;
        
        /**
         * @var string $table
         */
        private $table;

        /**
         * @var array $insert
         */
        private $insert = [];
        
        /**
         * @var array $update
         */
        private $update = [];

        /**
         * @var string $select
         */
        private $select = '';

        /**
         * @var bool $delete
         */
        private $delete = false;
        
        /**
         * @var string $leftJoin
         */
        private $leftJoin = '';
        
        /**
         * @var string $rightJoin
         */
        private $rightJoin = '';
        
        /**
         * @var string $crossJoin
         */
        private $crossJoin = '';

        /**
         * @var string $where
         */
        private $where = '';

        /**
         * @var array $andWhere
         */
        private $andWhere = [];
        
        /**
         * @var array $orWhere
         */
        private $orWhere = [];

        /**
         * @var string $orderBy
         */
        private $orderBy = '';

        /**
         * @var string $limit
         */
        private $limit = '';

        /**
         * Set PDO connection and table.
         *
         * @param \PDO $pdo
         * @param string $table
         */
        public function __construct(\PDO $pdo, string $table)
        {
            $this->pdo   = $pdo;
            $this->table = $table;
        }

        /**
         * Insert query.
         *
         * @param array $data
         * @return Query
         */
        public function insert(array $data): Query
        {
            $this->insert = $data;
            $query = $this->execute();
            return $query;
        }

        /**
         * Update query.
         *
         * @param array $data
         * @return Query
         */
        public function update(array $data): Query
        {
            $this->update = $data;
            $query = $this->execute();
            return $query;
        }

        /**
         * Delete query.
         *
         * @param array $data
         * @return Query
         */
        public function delete(): Query
        {
            $this->delete = true;
            $query = $this->execute();
            return $query;
        }

        /**
         * Select query.
         *
         * @param string[] $field
         * @return Query
         */
        public function select(...$field): Query
        {
            $this->select = (!empty($field)) ? join(', ', $field) : '*';
            $query = $this->execute();
            return $query;
        }

        /**
         * Left join query.
         *
         * @param string $join
         * @return self
         */
        public function leftJoin(string $join): self
        {
            $this->leftJoin = $join;
            return $this;
        }

        /**
         * Right join query.
         *
         * @param string $join
         * @return self
         */
        public function rightJoin(string $join): self 
        {
            $this->rightJoin = $join;
            return $this;
        }

        /**
         * Cross join query.
         *
         * @param string $join
         * @return self
         */
        public function crossJoin(string $join): self 
        {
            $this->crossJoin = $join;
            return $this;
        }

        /**
         * Where clauses.
         *
         * @param string $field
         * @param mixed $operatorValue
         * @param mixed|null $value
         * @return self
         */
        public function where(string $field, $operatorValue, $value = null): self
        {
            $operators = ['=', '>', '>=', '<', '<='. '!='];
            
            if (in_array($operatorValue, $operators)) {
                
                $this->where = [
                    'field'    => $field,
                    'operator' => $operatorValue,
                    'value'    => $value
                ];

            } else {
                
                $this->where = [
                    'field'    => $field,
                    'operator' => '=',
                    'value'    => $operatorValue
                ];
            }
            return $this;
        }

        /**
         * And where clauses.
         *
         * @param string $field
         * @param mixed $operatorValue
         * @param mixed|null $value
         * @return self
         */
        public function andWhere($field, $operatorValue, $value = null): self
        {
            $operators = ['=', '>', '>=', '<', '<='. '!='];
            
            if (in_array($operatorValue, $operators)) {
                
                $this->andWhere[] = [
                    'field'    => $field,
                    'operator' => $operatorValue,
                    'value'    => $value
                ];

            } else {
                
                $this->andWhere[] = [
                    'field'    => $field,
                    'operator' => '=',
                    'value'    => $operatorValue
                ];
            }
            return $this;
        }

        /**
         * Or where clauses.
         *
         * @param string $field
         * @param mixed $operatorValue
         * @param mixed|null $value
         * @return self
         */
        public function orWhere($field, $operatorValue, $value = null): self
        {
            $operators = ['=', '>', '>=', '<', '<='. '!='];
            
            if (in_array($operatorValue, $operators)) {
                
                $this->orWhere[] = [
                    'field'    => $field,
                    'operator' => $operatorValue,
                    'value'    => $value
                ];

            } else {
                
                $this->orWhere[] = [
                    'field'    => $field,
                    'operator' => '=',
                    'value'    => $operatorValue
                ];
            }
            return $this;
        }

        /**
         * Order by query.
         *
         * @param string $field
         * @param string $flag
         * @return self
         */
        public function orderBy(string $field, string $flag = 'ASC'): self
        {
            $this->orderBy = $field.' '.$flag;
            return $this;
        }

        /**
         * Limit query.
         *
         * @param int $start
         * @param int|null $end
         * @return self
         */
        public function limit(int $start, int $end = null): self
        {
            $this->limit = rtrim($start.','.$end, ',');
            return $this;
        }

        /**
         * Execute query.
         *
         * @return Query
         */
        private function execute(): Query
        {
            $query  = '';
            $values = [];

            // select
            if ($this->select) {
                $query .= 'SELECT '.$this->select. ' FROM '.$this->table;
            }

            // insert
            if (!empty($this->insert)) {
                $values      = $this->insert;
                $queryKeys   = implode(", ", array_keys($values));
                $queryValues = ':'.implode(", :", array_keys($values));
                $query       .= 'INSERT INTO '.$this->table.' ('.$queryKeys.') VALUES ('.$queryValues.')';
            }

            // update
            if (!empty($this->update)) {
                $set      = $this->update;
                $querySet = NULL;
                foreach($set as $key => $value){
                    $querySet .= "$key = :$key,";
                    $values[$key] = $value;
                }
                $querySet = rtrim($querySet, ',');
                $query .= 'UPDATE '.$this->table.' SET '.$querySet;
            }

            // delete
            if ($this->delete) {
                $query .= 'DELETE FROM '.$this->table;
            }

            // left join
            if ($this->leftJoin) {
                $query .= ' LEFT JOIN '.$this->leftJoin;
            }

            // right join
            if ($this->rightJoin) {
                $query .= ' RIGHT JOIN '.$this->rightJoin;
            }

            // cross join
            if ($this->crossJoin) {
                $query .= ' CROSS JOIN '.$this->crossJoin;
            }

            // where
            if (!empty($this->where)) {
                $whereRaw = $this->where['field'].' '.$this->where['operator'].' :'.$this->where['field'];
                $query .= ' WHERE '.$whereRaw;
                $values[$this->where['field']] = $this->where['value'];
            }

            // and where
            if (!empty($this->andWhere)) {
                foreach ($this->andWhere as $andWhere) {
                    $andWhereRaw = $andWhere['field'].' '.$andWhere['operator'].' :'.$andWhere['field'];
                    $query .= ' AND '.$andWhereRaw;
                    $values[$andWhere['field']] = $andWhere['value'];
                }
            }

            // or where
            if (!empty($this->orWhere)) {
                foreach ($this->orWhere as $orWhere) {
                    $orWhereRaw = $orWhere['field'].' '.$orWhere['operator'].' :'.$orWhere['field'];
                    $query .= ' OR '.$orWhere;
                    $values[$orWhere['field']] = $orWhere['value'];
                }
            }

            // order by
            if ($this->orderBy) {
                $query .= ' ORDER BY '.$this->orderBy;
            }

            // limit
            if ($this->limit) {
                $query .= ' LIMIT '.$this->limit;
            }

            return (new Query($this->pdo))
                ->query($query)
                ->values($values);
        }
    }