<?php
    
    /**
     * AliasesTrait
     * 
     * @author Davide Cesarano <davide.cesarano@unipegaso.it>
     * @link https://github.com/davidecesarano/embryo-pdo 
     */

    namespace Embryo\PDO\QueryBuilder\Traits;

    trait AliasesTrait 
    {
        /**
         * ------------------------------------------------------------
         * AND
         * ------------------------------------------------------------
         */

        /**
         * Alias of where().
         *
         * @param string $field
         * @param mixed $operatorValue
         * @param mixed|null $value
         * @return self
         */
        public function andWhere($field, $operatorValue = null, $value = null): self
        {
            return $this->where($field, $operatorValue, $value);
        }

        /**
         * Alias of where().
         *
         * @param string $field
         * @param mixed $operatorValue
         * @param mixed|null $value
         * @return self
         */
        public function and($field, $operatorValue = null, $value = null): self
        {
            return $this->where($field, $operatorValue, $value);
        }

        /**
         * ------------------------------------------------------------
         * OR
         * ------------------------------------------------------------
         */

        /**
         * Alias of orWhere().
         *
         * @param string $field
         * @param mixed $operatorValue
         * @param mixed|null $value
         * @return self
         */
        public function or($field, $operatorValue = null, $value = null): self
        {
            return $this->orWhere($field, $operatorValue, $value);
        }

        /**
         * ------------------------------------------------------------
         * IS NULL
         * ------------------------------------------------------------
         */

        /**
         * Alias of whereNull().
         *
         * @param string $field
         * @return self
         */
        public function whereIsNull(string $field): self 
        {
            return $this->whereNull($field);
        }

        /**
         * Alias of whereNull().
         *
         * @param string $field
         * @return self
         */
        public function andWhereIsNull(string $field): self 
        {
            return $this->whereNull($field);
        }

        /**
         * Alias of whereNull().
         *
         * @param string $field
         * @return self
         */
        public function andWhereNull(string $field): self 
        {
            return $this->whereNull($field);
        }        

        /**
         * Alias of whereNull().
         *
         * @param string $field
         * @return self
         */
        public function andIsNull(string $field): self
        {
            return $this->whereNull($field);
        }

        /**
         * Alias of whereNull().
         *
         * @param string $field
         * @return self
         */
        public function andNull(string $field): self
        {
            return $this->whereNull($field);
        }

        /**
         * Alias of orWhereNull().
         *
         * @param string $field
         * @return self
         */
        public function orIsNull(string $field): self
        {
            return $this->orWhereNull($field);
        }

        /**
         * Alias of orWhereNull().
         *
         * @param string $field
         * @return self
         */
        public function orWhereIsNull(string $field): self
        {
            return $this->orWhereNull($field);
        }

        /**
         * Alias of orWhereNull().
         *
         * @param string $field
         * @return self
         */
        public function orNull(string $field): self
        {
            return $this->orWhereNull($field);
        }

        /**
         * ------------------------------------------------------------
         * IS NOT NULL
         * ------------------------------------------------------------
         */

        /**
         * Alias of whereNotNull().
         *
         * @param string $field
         * @return self
         */
        public function whereIsNotNull(string $field): self 
        {
            return $this->whereNotNull($field);
        }

        /**
         * Alias of whereNotNull().
         *
         * @param string $field
         * @return self
         */
        public function andWhereIsNotNull(string $field): self 
        {
            return $this->whereNotNull($field);
        }

        /**
         * Alias of whereNotNull().
         *
         * @param string $field
         * @return self
         */
        public function andWhereNotNull(string $field): self 
        {
            return $this->whereNotNull($field);
        }

        /**
         * Alias of whereNotNull().
         *
         * @param string $field
         * @return self
         */
        public function andIsNotNull(string $field): self 
        {
            return $this->whereNotNull($field);
        }

        /**
         * Alias of whereNotNull().
         *
         * @param string $field
         * @return self
         */
        public function andNotNull(string $field): self
        {
            return $this->whereNotNull($field);
        }

        /**
         * Alias of orWhereNotNull().
         *
         * @param string $field
         * @return self
         */
        public function orIsNotNull($field): self
        {
            return $this->orWhereNotNull($field);
        }

        /**
         * Alias of orWhereNotNull().
         *
         * @param string $field
         * @return self
         */
        public function orNotNull($field): self
        {
            return $this->orWhereNotNull($field);
        }

        /**
         * Alias of orWhereNotNull().
         *
         * @param string $field
         * @return self
         */
        public function orWhereIsNotNull($field): self
        {
            return $this->orWhereNotNull($field);
        }

        /**
         * ------------------------------------------------------------
         * IN
         * ------------------------------------------------------------
         */

        /**
         * Alias of whereIn().
         *
         * @param string $field 
         * @param array $values 
         * @return self
         */
        public function andWhereIn(string $field, array $values): self 
        {
            return $this->whereIn($field, $values);
        }

        /**
         * Alias of whereIn().
         *
         * @param string $field 
         * @param array $values 
         * @return self
         */
        public function andIn(string $field, array $values): self 
        {
            return $this->whereIn($field, $values);
        }

        /**
         * Alias of orWhereIn().
         *
         * @param string $field 
         * @param array $values 
         * @return self
         */
        public function orIn(string $field, array $values): self 
        {
            return $this->orWhereIn($field, $values);
        }

        /**
         * ------------------------------------------------------------
         * NOT IN
         * ------------------------------------------------------------
         */

        /**
         * Alias of whereNotIn().
         * 
         * @param string $field 
         * @param array $values 
         * @return self
         */
        public function andNotIn(string $field, array $values): self 
        {
            return $this->whereNotIn($field, $values);
        }

        /**
         * Alias of whereNotIn().
         * 
         * @param string $field 
         * @param array $values 
         * @return self
         */
        public function andWhereNotIn(string $field, array $values): self 
        {
            return $this->whereNotIn($field, $values);
        }

        /**
         * Alias of orWhereNotIn().
         * 
         * @param string $field 
         * @param array $values 
         * @return self
         */
        public function orNotIn(string $field, array $values): self 
        {
            return $this->orWhereNotIn($field, $values);
        }

        /**
         * ------------------------------------------------------------
         * BETWEEN
         * ------------------------------------------------------------
         */

        /**
         * Alias of whereBetween().
         * 
         * @param string $field 
         * @param array $values 
         * @return self
         */
        public function andWhereBetween(string $field, array $values): self 
        {
            return $this->whereBetween($field, $values);
        }

        /**
         * Alias of whereBetween().
         * 
         * @param string $field 
         * @param array $values 
         * @return self
         */
        public function andBetween(string $field, array $values): self 
        {
            return $this->whereBetween($field, $values);
        }

        /**
         * Alias of orWhereBetween().
         * 
         * @param string $field 
         * @param array $values 
         * @return self
         */
        public function orBetween(string $field, array $values): self 
        {
            return $this->orWhereBetween($field, $values);
        }

        /**
         * ------------------------------------------------------------
         * NOT BETWEEN
         * ------------------------------------------------------------
         */

        /**
         * Alias of whereNotBetween().
         * 
         * @param string $field 
         * @param array $values 
         * @return self
         */
        public function andWhereNotBetween(string $field, array $values): self 
        {
            return $this->whereNotBetween($field, $values);
        }

        /**
         * Alias of whereNotBetween().
         * 
         * @param string $field 
         * @param array $values 
         * @return self
         */
        public function andNotBetween(string $field, array $values): self 
        {
            return $this->whereNotBetween($field, $values);
        }

        /**
         * Alias of orWhereBetween().
         * 
         * @param string $field 
         * @param array $values 
         * @return self
         */
        public function orNotBetween(string $field, array $values): self 
        {
            return $this->orWhereNotBetween($field, $values);
        }
    }