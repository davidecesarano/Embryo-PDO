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
         * @var array $leftJoin
         */
        private $leftJoin = [];
        
        /**
         * @var array $rightJoin
         */
        private $rightJoin = [];
        
        /**
         * @var array $crossJoin
         */
        private $crossJoin = [];

        /**
         * @var array $innerJoin
         */
        private $innerJoin = [];

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
            $this->leftJoin[] = $join;
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
            $this->rightJoin[] = $join;
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
            $this->crossJoin[] = $join;
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
            $operators = ['=', '>', '>=', '<', '<=', '!='];
            
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
            $operators = ['=', '>', '>=', '<', '<=', '!='];
            
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
            $operators = ['=', '>', '>=', '<', '<=', '!='];
            
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
            if (!empty($this->leftJoin)) {
                foreach ($this->leftJoin as $join) {
                    $query .= ' LEFT JOIN '.$join;
                }
            }

            // right join
            if (!empty($this->rightJoin)) {
                foreach ($this->rightJoin as $join) {
                    $query .= ' RIGHT JOIN '.$join;
                }
            }

            // cross join
            if (!empty($this->crossJoin)) {
                foreach ($this->crossJoin as $join) {
                    $query .= ' CROSS JOIN '.$join;
                }
            }

            // inner join
            if (!empty($this->innerJoin)) {
                foreach ($this->innerJoin as $join) {
                    $query .= ' INNER JOIN '.$join;
                }
            }

            // where
            if (!empty($this->where)) {
                $field = str_replace('.', '', $this->where['field']);
                $whereRaw = $this->where['field'].' '.$this->where['operator'].' :'.$field;
                $query .= ' WHERE '.$whereRaw;
                $values[$field] = $this->where['value'];
            }

            // and where
            if (!empty($this->andWhere)) {
                foreach ($this->andWhere as $andWhere) {
                    $field = str_replace('.', '', $andWhere['field']);
                    $andWhereRaw = $andWhere['field'].' '.$andWhere['operator'].' :'.$field;
                    $query .= ' AND '.$andWhereRaw;
                    $values[$field] = $andWhere['value'];
                }
            }

            // or where
            if (!empty($this->orWhere)) {
                foreach ($this->orWhere as $orWhere) {
                    $field = str_replace('.', '', $orWhere['field']);
                    $orWhereRaw = $orWhere['field'].' '.$orWhere['operator'].' :'.$field;
                    $query .= ' OR '.$orWhere;
                    $values[$field] = $orWhere['value'];
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