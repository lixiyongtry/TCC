<?php declare(strict_types=1);


namespace Swoft\Db\Concern;

use Closure;
use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\Db\Eloquent\Model;
use Swoft\Db\Exception\DbException;
use Swoft\Db\Query\Builder;

/**
 * Class BuildsQueries
 *
 * @since 2.0
 */
trait BuildsQueries
{
    /**
     * Chunk the results of the query.
     *
     * @param int      $count
     * @param callable $callback
     *
     * @return bool
     * @throws ContainerException
     * @throws ReflectionException
     * @throws DbException
     */
    public function chunk($count, callable $callback)
    {
        $this->enforceOrderBy();

        $page = 1;

        do {
            // We'll execute the query for the given page and get the results. If there are
            // no results we can just break and return from here. When there are results
            // we will call the callback with the current chunk of these results here.
            /* @var \Swoft\Db\Eloquent\Builder|\Swoft\Db\Eloquent\Builder $builder */
            $builder = $this->forPage($page, $count);
            $results = $builder->get();

            $countResults = $results->count();
            if ($countResults == 0) {
                break;
            }

            // On each chunk result set, we will pass them to the callback and then let the
            // developer take care of everything within the callback, which allows us to
            // keep the memory low for spinning through large result sets for working.
            if ($callback($results, $page) === false) {
                return false;
            }

            unset($results);

            $page++;
        } while ($countResults == $count);

        return true;
    }

    /**
     * Execute a callback over each item while chunking.
     *
     * @param callable $callback
     * @param int      $count
     *
     * @return bool
     * @throws ContainerException
     * @throws ReflectionException
     * @throws DbException
     */
    public function each(callable $callback, $count = 1000)
    {
        return $this->chunk($count, function ($results) use ($callback) {
            foreach ($results as $key => $value) {
                if ($callback($value, $key) === false) {
                    return false;
                }
            }

            return true;
        });
    }

    /**
     * Execute the query and get the first result.
     *
     * @param array $columns
     *
     * @return Model|object|static|null
     * @throws ContainerException
     * @throws ReflectionException
     * @throws DbException
     */
    public function first(array $columns = ['*'])
    {
        /* @var \Swoft\Db\Eloquent\Builder|\Swoft\Db\Eloquent\Builder $builder */
        $builder = $this->take(1);
        return $builder->get($columns)->first();
    }

    /**
     * Apply the callback's query changes if the given "value" is true.
     *
     * @param  mixed    $value
     * @param  callable $callback
     * @param  callable $default
     *
     * @return mixed|$this
     */
    public function when($value, $callback, $default = null)
    {
        if ($value) {
            return $callback($this, $value) ?: $this;
        } elseif ($default) {
            return $default($this, $value) ?: $this;
        }

        return $this;
    }

    /**
     * Pass the query to a given callback.
     *
     * @param  Closure $callback
     *
     * @return Builder
     */
    public function tap($callback)
    {
        return $this->when(true, $callback);
    }

    /**
     * Apply the callback's query changes if the given "value" is false.
     *
     * @param  mixed    $value
     * @param  callable $callback
     * @param  callable $default
     *
     * @return mixed|$this
     */
    public function unless($value, $callback, $default = null)
    {
        if (!$value) {
            return $callback($this, $value) ?: $this;
        } elseif ($default) {
            return $default($this, $value) ?: $this;
        }

        return $this;
    }
}
