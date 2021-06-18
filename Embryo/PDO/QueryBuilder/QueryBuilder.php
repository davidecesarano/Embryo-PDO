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
    
    use Embryo\Http\Factory\ServerRequestFactory;
    use Embryo\PDO\QueryBuilder\Query;
    use Embryo\PDO\QueryBuilder\Traits\{AliasesTrait, ComposeQueryTrait};
    
    class QueryBuilder
    {
        use AliasesTrait;
        use ComposeQueryTrait;

        /**
         * @var \PDO $pdo
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
         * @var array $join
         */
        private $join = [];

        /**
         * @var array $where
         */
        private $where = [];

        /**
         * @var string $groupBy
         */
        private $groupBy = '';

        /**
         * @var string $orderBy
         */
        private $orderBy = '';

        /**
         * @var string $limit
         */
        private $limit = '';

        /**
         * @var int|bool $offset
         */
        private $offset = false;

        /**
         * @var array $whereOperators
         */
        private $whereOperators = [
            '=', 
            '>', 
            '>=', 
            '<', 
            '<=', 
            '!=', 
            '<>', 
            'LIKE',
            'NOT LIKE', 
            'IS NULL', 
            'IS NOT NULL', 
            'IN', 
            'NOT IN',
            'BETWEEN',
            'NOT BETWEEN',
            'REGEXP'
        ];

        /**
         * @var bool $startParentheses
         */
        private $startParentheses = false;

        /**
         * @var bool $endParentheses
         */
        private $endParentheses = false;

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
         * ------------------------------------------------------------
         * STATEMENTS
         * ------------------------------------------------------------
         */

        /**
         * "INSERT" query.
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
         * "UPDATE" query.
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
         * "DELETE" query.
         *
         * @return Query
         */
        public function delete(): Query
        {
            $this->delete = true;
            $query = $this->execute();
            return $query;
        }

        /**
         * "SELECT" query.
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
         * SELECT statement shortened.
         * 
         * @return object|array|bool
         */
        public function get()
        {
            $this->select = '*';
            $query = $this->execute();
            return $query->get();
        }

        /**
         * SELECT statement shortened
         * for to force array.
         * 
         * @return array
         */
        public function all()
        {
            $this->select = '*';
            $query = $this->execute();
            return $query->all();
        }

        /**
         * Pagination.
         * 
         * @param int $perPage
         * @param string $select 
         * @return object
         */
        public function paginate(int $perPage = 50, string $select = '*'): object
        {
            $this->select = $select;

            $request = (new ServerRequestFactory)->createServerRequestFromServer();
            $params  = $request->getQueryParams();
            $page    = isset($params['page']) && $params['page'] != 0 && is_numeric($params['page']) ? (int) $params['page'] : 1;
            $query   = $this->execute();
            $total   = $query->count();
            $offset  = ($page-1) * $perPage;
            $pages   = ceil($total / $perPage);
            $next    = ($page == $pages) ? NULL : $page+1;
            $prev    = ($page == 1) ? NULL : $page-1;
            $from    = $offset+1;
            $to      = $offset+$perPage < $total ? $offset+$perPage : $total;

            $this->limit = $offset.', '.$perPage;
            $query = $this->execute();
            $data = $query->all();

            return (object) [
                'total'        => $total,
                'per_page'     => $perPage,
                'current_page' => $page,
                'last_page'    => $pages,
                'next_page'    => $next,
                'prev_page'    => $prev,
                'from'         => $from,
                'to'           => $to,
                'data'         => $data
            ];
        }

        /**
         * Debug query shortened.
         * 
         * Works only for SELECT statement.
         * 
         * @return string
         */
        public function debug(): string
        {
            $this->select = '*';
            $query = $this->execute();
            return $query->debug();
        }

        /**
         * ------------------------------------------------------------
         * AGGREGATES
         * ------------------------------------------------------------
         */

        /**
         * Return rows count.
         * 
         * @return int
         */
        public function count(): int
        {
            $this->select = '*';
            $query = $this->execute();
            return $query->count();
        }

        /**
         * Return max value.
         * 
         * @param string $field 
         * @return mixed
         */
        public function max(string $field) 
        {
            $max = 'MAX('.$field.')';
            $this->select = $max;
            $query = $this->execute();
            return $query->get()->{$max};
        }

        /**
         * Return min value.
         * 
         * @param string $field 
         * @return mixed
         */
        public function min(string $field) 
        {
            $min = 'MIN('.$field.')';
            $this->select = $min;
            $query = $this->execute();
            return $query->get()->{$min};
        }

        /**
         * Return average value.
         * 
         * @param string $field 
         * @return mixed
         */
        public function avg(string $field) 
        {
            $avg = 'AVG('.$field.')';
            $this->select = $avg;
            $query = $this->execute();
            return $query->get()->{$avg};
        }

        /**
         * Return sum value's column.
         * 
         * @param string $field 
         * @return mixed
         */
        public function sum(string $field) 
        {
            $sum = 'SUM('.$field.')';
            $this->select = $sum;
            $query = $this->execute();
            return $query->get()->{$sum};
        }

        /**
         * ------------------------------------------------------------
         * JOIN
         * ------------------------------------------------------------
         */

        /**
         * LEFT JOIN.
         *
         * @param string $join
         * @return self
         */
        public function leftJoin(string $join): self
        {
            $this->join[] = [
                'type' => 'LEFT',
                'sql'  => trim($join)
            ];
            return $this;
        }

        /**
         * RIGHT JOIN.
         *
         * @param string $join
         * @return self
         */
        public function rightJoin(string $join): self 
        {
            $this->join[] = [
                'type' => 'RIGHT',
                'sql'  => trim($join)
            ];
            return $this;
        }

        /**
         * CROSS JOIN.
         *
         * @param string $join
         * @return self
         */
        public function crossJoin(string $join): self 
        {
            $this->join[] = [
                'type' => 'CROSS',
                'sql'  => trim($join)
            ];
            return $this;
        }

        /**
         * INNER JOIN.
         *
         * @param string $join
         * @return self
         */
        public function innerJoin(string $join): self 
        {
            $this->join[] = [
                'type' => 'INNER',
                'sql'  => trim($join)
            ];
            return $this;
        }

        /**
         * Raw join.
         *
         * @param string $join
         * @return self
         */
        public function rawJoin(string $join): self 
        {
            $this->join[] = [
                'type' => '',
                'sql'  => trim($join)
            ];
            return $this;
        }

        /**
         * ------------------------------------------------------------
         * WHERE CONDITION
         * ------------------------------------------------------------
         */

        /**
         * "WHERE" and "AND" condition.
         *
         * @param string|callable $field
         * @param mixed $operatorValue
         * @param mixed|null $value
         * @return self
         */
        public function where($field, $operatorValue = null, $value = null): self
        {   
            return $this->addWhere('AND', $field, $operatorValue, $value);
        }

        /**
         * "OR" condition.
         *
         * @param string|callable $field
         * @param mixed $operatorValue
         * @param mixed|null $value
         * @return self
         */
        public function orWhere($field, $operatorValue, $value = null): self
        {
            return $this->addWhere('OR', $field, $operatorValue, $value);
        }

        /**
         * "WHERE IS NULL" and "AND IS NULL" condition.
         *
         * @param string $field
         * @return self
         */
        public function whereNull(string $field): self 
        {
            return $this->addWhere('AND', $field, 'IS NULL');
        }

        /**
         * "OR IS NULL" condition.
         *
         * @param string $field
         * @return self
         */
        public function orWhereNull(string $field): self 
        {
            return $this->addWhere('OR', $field, 'IS NULL');
        }

        /**
         * "WHERE IS NOT NULL" and "AND IS NOT NULL" condition.
         *
         * @param string $field
         * @return self
         */
        public function whereNotNull(string $field): self 
        {
            return $this->addWhere('AND', $field, 'IS NOT NULL');
        }

        /**
         * "OR IS NOT NULL" condition.
         *
         * @param string $field
         * @return self
         */
        public function orWhereNotNull(string $field): self 
        {
            return $this->addWhere('OR', $field, 'IS NOT NULL');
        }

        /**
         * "WHERE IN" and "AND IN" condition.
         * 
         * @param string $field 
         * @param array $values 
         * @return self
         */
        public function whereIn(string $field, array $values): self 
        {
            return $this->addWhere('AND', $field, 'IN', $values);
        }

        /**
         * "OR IN" condition.
         * 
         * @param string $field 
         * @param array $values 
         * @return self
         */
        public function orWhereIn(string $field, array $values): self 
        {
            return $this->addWhere('OR', $field, 'IN', $values);
        }

        /**
         * "WHERE NOT IN" and "AND NOT IN" condition.
         * 
         * @param string $field 
         * @param array $values 
         * @return self
         */
        public function whereNotIn(string $field, array $values): self 
        {
            return $this->addWhere('AND', $field, 'NOT IN', $values);
        }

        /**
         * "OR NOT IN" condition.
         * 
         * @param string $field 
         * @param array $values 
         * @return self
         */
        public function orWhereNotIn(string $field, array $values): self 
        {
            return $this->addWhere('OR', $field, 'NOT IN', $values);
        }
        
        /**
         * "WHERE BETWEEN" and "AND BETWEEN" condition.
         * 
         * @param string $field 
         * @param array $values 
         * @return self
         * @throws \InvalidArgumentException
         */
        public function whereBetween(string $field, array $values): self 
        {
            if (count($values) !== 2) {
                throw new \InvalidArgumentException('Between condition must have two values');
            }
            return $this->addWhere('AND', $field, 'BETWEEN', $values);
        }

        /**
         * "OR BETWEEN" condition.
         * 
         * @param string $field 
         * @param array $values 
         * @return self
         * @throws \InvalidArgumentException
         */
        public function orWhereBetween(string $field, array $values): self 
        {
            if (count($values) !== 2) {
                throw new \InvalidArgumentException('Between condition must have two values');
            }
            return $this->addWhere('OR', $field, 'BETWEEN', $values);
        }

        /**
         * "WHERE NOT BETWEEN" and "AND NOT BETWEEN" condition.
         * 
         * @param string $field 
         * @param array $values 
         * @return self
         * @throws \InvalidArgumentException
         */
        public function whereNotBetween(string $field, array $values): self 
        {
            if (count($values) !== 2) {
                throw new \InvalidArgumentException('Between condition must have two values');
            }
            return $this->addWhere('AND', $field, 'NOT BETWEEN', $values);
        }

        /**
         * "OR NOT BETWEEN" condition.
         * 
         * @param string $field 
         * @param array $values 
         * @return self
         * @throws \InvalidArgumentException
         */
        public function orWhereNotBetween(string $field, array $values): self 
        {
            if (count($values) !== 2) {
                throw new \InvalidArgumentException('Between condition must have two values');
            }
            return $this->addWhere('OR', $field, 'NOT BETWEEN', $values);
        }

        /**
         * Raw where condition.
         * 
         * @param string $where
         * @param array $values
         * @return self
         */
        public function rawWhere(string $where, array $values = []): self
        {
           return $this->addWhere('', $where, null, $values);
        }

        /**
         * Add WHERE condition.
         * 
         * @param string $clause 
         * @param string|callable $field 
         * @param mixed $operatorValue 
         * @param mixed $value 
         * @return self
         * @throws \InvalidArgumentException
         */
        private function addWhere($clause, $field, $operatorValue = null, $value = null): self
        {
            if (!is_string($field) && !is_callable($field)) {
                throw new \InvalidArgumentException("First parameter must be a string or callable");
            }

            if (is_callable($field)) {

                $this->startParentheses = true;  
                call_user_func($field, $this);
                $last = (count($this->where))-1;
                $this->where[$last]['endParentheses'] = true;

            } else {
 
                $operator = ($operatorValue !== 0 && in_array($operatorValue, $this->whereOperators)) ? $operatorValue : '=';
                $this->where[] = [
                    'clause'           => $clause,
                    'field'            => trim($field),
                    'operator'         => trim($operator),
                    'value'            => ($value) ? $value : $operatorValue,
                    'startParentheses' => $this->startParentheses,
                    'endParentheses'   => $this->endParentheses
                ];

            }
            $this->startParentheses = false;
            return $this;
        }

        /**
         * ------------------------------------------------------------
         * ORDERING, GROUPING, LIMIT AND OFFSET
         * ------------------------------------------------------------
         */

        /**
         * Group by query.
         *
         * @param string $groupBy
         * @return self
         */
        public function groupBy(string $groupBy): self 
        {
            $this->groupBy = trim($groupBy);
            return $this;
        }

        /**
         * Order by query.
         *
         * @param string $orderBy
         * @return self
         */
        public function orderBy(string $orderBy): self
        {
            $this->orderBy = trim($orderBy);
            return $this;
        }

        /**
         * Limit query.
         *
         * @param string $limit
         * @return self
         */
        public function limit(string $limit): self
        {
            $this->limit = trim($limit);
            return $this;
        }

        /**
         * Offset query.
         * 
         * @param int $offset
         * @return self
         */
        public function offset(int $offset): self
        {
            $this->offset = $offset;
            return $this;
        }

        /**
         * ------------------------------------------------------------
         * COMPOSE AND EXECUTE QUERY
         * ------------------------------------------------------------
         */

        /**
         * Execute query.
         *
         * @return Query
         */
        private function execute(): Query
        {
            $query = $this->composeStatement()['query'];
            $query .= $this->composeJoins();
            $query .= $this->composeWhereConditions()['query'];          
            $query .= $this->composeGroupBy();
            $query .= $this->composeOrderBy();
            $query .= $this->composeLimit();
            $query .= $this->composeOffset();
            $values = array_merge($this->composeStatement()['values'], $this->composeWhereConditions()['values']);

            return (new Query($this->pdo))
                ->query($query)
                ->values($values);
        }
    }