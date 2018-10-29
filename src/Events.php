<?php

namespace EventyClassic;

class Events
{

    /**
     * Holds all registered actions.
     *
     * @var Action
     */
    protected $action;

    /**
     * Holds all registered filters.
     *
     * @var Filter
     */
    protected $filter;

    /**
     * Constructs the class.
     *
     * @param null|Action $action
     * @param null|Filter $filter
     */
    public function __construct(Action $action = null, Filter $filter = null)
    {
        if (!$action) {
            $this->action = new Action;
        }
        if (!$filter) {
            $this->filter = new Filter;
        }
    }

    /**
     * Gets the action instance.
     *
     * @return Action
     */
    public function getAction()
    {
        return $this->action;
    }


    /**
     * Gets the action instance.
     *
     * @return Filter
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Adds an action.
     *
     * @param string  $hook      Hook name.
     * @param mixed   $callback  Function to execute.
     * @param integer $priority  Priority of the action.
     * @param integer $arguments Number of arguments to accept.
     *
     * @return Events
     */
    public function addAction($hook, $callback, $priority = 20, $arguments = 1)
    {
        $this->action->listen($hook, $callback, $priority, $arguments);

        return $this;
    }

    /**
     * Removes an action.
     *
     * @param string $hook Hook name.
     * @param mixed $callback Function to execute.
     * @param int $priority Priority of the action.
     */
    public function removeAction($hook, $callback, $priority = 20)
    {
        $this->action->remove($hook, $callback, $priority);
    }

    /**
     * Removes all actions.
     *
     * @param string $hook Hook name.
     */
    public function removeAllActions($hook = null)
    {
        $this->action->removeAll($hook);
    }

    /**
     * Adds a filter.
     *
     * @param string  $hook      Hook name.
     * @param mixed   $callback  Function to execute.
     * @param integer $priority  Priority of the action.
     * @param integer $arguments Number of arguments to accept.
     *
     * @return Events
     */
    public function addFilter($hook, $callback, $priority = 20, $arguments = 1)
    {
        $this->filter->listen($hook, $callback, $priority, $arguments);

        return $this;
    }

    /**
     * Removes a filter.
     *
     * @param string $hook     Hook name.
     * @param mixed  $callback Function to execute.
     * @param int    $priority Priority of the action.
     */
    public function removeFilter($hook, $callback, $priority = 20)
    {
        $this->filter->remove($hook, $callback, $priority);
    }

    /**
     * Removes all filters.
     *
     * @param string $hook Hook name
     */
    public function removeAllFilters($hook = null)
    {
        $this->filter->removeAll($hook);
    }

    /**
     * Runs an action.
     *
     * Actions never return anything. It is merely a way of executing code at a specific time in your code.
     * You can add as many parameters as you'd like.
     *
     * @param array ...$args First argument will be the name of the hook, and the rest will be args for the hook.
     *
     * @return void
     */
    public function runAction(...$args)
    {
        $hook = $this->createHook($args);
        $this->action->fire($hook->name, $hook->args);
    }

    /**
     * Runs a filter.
     *
     * Filters should always return something. The first parameter will always be the default value.
     * You can add as many parameters as you'd like.
     *
     * @param array ...$args First argument will be the name of the hook, and the rest will be args for the hook.
     *
     * @return mixed
     */
    public function runFilter(...$args)
    {
        $hook = $this->createHook($args);
        return $this->filter->fire($hook->name, $hook->args);
    }

    /**
     * Figures out the hook.
     *
     * Will return an object with two keys. One for the name and one for the arguments that will be
     * passed to the hook itself.
     *
     * @param mixed ...$args
     *
     * @return \stdClass
     */
    protected function createHook($args)
    {
        return (object)[
            'name' => $args[0],
            'args' => array_values(array_slice($args, 1))
        ];
    }

}
