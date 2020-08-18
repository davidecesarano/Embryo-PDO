<?php

    /**
     * ComposeQueryTrait
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link https://github.com/davidecesarano/embryo-pdo 
     */

    namespace Embryo\PDO\QueryBuilder\Traits;

    trait ComposeQueryTrait 
    {
        /**
         * Compose SELECT, UPDATE, INSERT or DELETE
         * query with values.
         * 
         * @return array
         */
        protected function composeStatement(): array
        {
            $query = '';
            $values = [];

            // select
            if ($this->select) {
                $query = 'SELECT '.$this->select. ' FROM '.$this->table;
            }

            // insert
            if (!empty($this->insert)) {
                $values = $this->insert;
                $queryKeys = implode(", ", array_keys($values));
                $queryValues = ':'.implode(", :", array_keys($values));
                $query = 'INSERT INTO '.$this->table.' ('.$queryKeys.') VALUES ('.$queryValues.')';
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
                $query = 'UPDATE '.$this->table.' SET '.$querySet;
            }

            // delete
            if ($this->delete) {
                $query = 'DELETE FROM '.$this->table;
            }

            return [
                'query' => $query,
                'values' => $values
            ];
        }

        /**
         * Compose joins.
         * 
         * @return string
         */
        protected function composeJoins(): string
        {
            $query = '';
            if (!empty($this->join)) {
                foreach ($this->join as $join) {
                    if ($join['type'] === '') {
                        $query .= ' '.$join['sql'];
                    } else {
                        $query .= ' '.$join['type'].' JOIN '.$join['sql'];
                    }
                }
            }
            return $query;
        }

        /**
         * Compose where conditions with
         * values.
         * 
         * @return array
         */
        protected function composeWhereConditions(): array
        {
            $query = '';
            $values = [];

            if (!empty($this->where)) {
                foreach ($this->where as $i => $where) {

                    if (in_array($where['operator'], ['IS NULL', 'IS NOT NULL'])) {

                        $whereRaw = $where['field'].' '.$where['operator'];

                    } else if (in_array($where['operator'], ['IN', 'NOT IN'])) {

                        $in = '';
                        $field = str_replace('.', '', $where['field']);
                        foreach ($where['value'] as $x => $item) {
                            $key = $field.'in'.$x;
                            $in .= ":$key, ";
                            $values[$key] = $item;
                        }
                        $in = rtrim($in, ', ');
                        $whereRaw = $where['field'].' '.$where['operator'].' ('.$in.')';
                    
                    } else if (in_array($where['operator'], ['BETWEEN', 'NOT BETWEEN'])) {
                        
                        $field = str_replace('.', '', $where['field']);
                        $whereRaw = $where['field'].' '.$where['operator'].' :'.$field.'btw0 AND :'.$field.'btw1';
                        $values[$field.'btw0'] = $where['value'][0];
                        $values[$field.'btw1'] = $where['value'][1];

                    } else if ($where['clause'] === '') {

                        $whereRaw = $where['field'];
                        $values = array_merge($values, $where['value']);

                    } else {
                        $field          = str_replace('.', '', $where['field']).$i;
                        $whereRaw       = $where['field'].' '.$where['operator'].' :'.$field;
                        $values[$field] = $where['value'];
                    }

                    if ($where['clause'] !== '') {
                        if ($i == 0) {
                            $query .= ' WHERE '.$whereRaw;
                        } else {
                            $query .= ' '.$where['clause'].' ';
                            $query .= $where['startParentheses'] ? '(': '';
                            $query .= $whereRaw;
                            $query .= $where['endParentheses'] ? ')': '';
                        }
                    } else {
                        $query .= ' '.$whereRaw.' ';
                    }

                }  
            }

            return [
                'query' => $query,
                'values' => $values
            ];
        }

        /**
         * Compose GROUP BY.
         * 
         * @return string
         */
        protected function composeGroupBy(): string
        {
            $query = '';
            if ($this->groupBy) {
                $query = ' GROUP BY '.$this->groupBy;
            }
            return $query;
        }

        /**
         * Compose ORDERB BY.
         * 
         * @return string
         */
        protected function composeOrderBy(): string
        {
            $query = '';
            if ($this->orderBy) {
                $query = ' ORDER BY '.$this->orderBy;
            }
            return $query;
        }

        /**
         * Compose LIMIT.
         * 
         * @return string
         */
        public function composeLimit(): string
        {
            $query = '';
            if ($this->limit) {
                $query = ' LIMIT '.$this->limit;
            }
            return $query;
        }

        /**
         * Compose OFFSET.
         * 
         * @return string
         */
        public function composeOffset(): string
        {
            $query = '';
            if ($this->offset) {
                $query = ' OFFSET '.$this->offset;
            }
            return $query;
        }
    }