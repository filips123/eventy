<?php

namespace EventyClassic;

abstract class Event
{
    /**
     * Holds the event listeners.
     *
     * @var array
     */
    protected $listeners = null;

    public function __construct()
    {
        $this->listeners = [];
    }

    /**
     * Adds a listener.
     *
     * @param string  $hook      Hook name.
     * @param mixed   $callback  Function to execute.
     * @param integer $priority  Priority of the action.
     * @param integer $arguments Number of arguments to accept.
     *
     * @return Event
     */
    public function listen($hook, $callback, $priority = 20, $arguments = 1)
    {
        $this->listeners[] = [
            'hook'      => $hook,
            'callback'  => $callback,
            'priority'  => $priority,
            'arguments' => $arguments,
        ];

        return $this;
    }

    /**
     * Removes a listener.
     *
     * @param string $hook     Hook name.
     * @param mixed  $callback Function to execute.
     * @param int    $priority Priority of the action.
     */
    public function remove($hook, $callback, $priority = 20)
    {
        if ($this->listeners) {
            $listeners = $this->listeners;
            foreach ($this->listeners as $key => $value) {
                if ($value['hook'] == $hook &&
                    $value['callback'] == $callback &&
                    $value['priority'] == $priority
                ) {
                    unset($listeners[$key]);
                }
            }
            $this->listeners = $listeners;
        }
    }

    /**
     * Remove all listeners with given hook in collection. If no hook, clear all listeners.
     *
     * @param string $hook Hook name
     */
    public function removeAll($hook = null)
    {
        if ($this->listeners) {
            if ($hook) {
                $listeners = $this->listeners;
                foreach ($this->listeners as $key => $value) {
                    if ($value['hook'] == $hook) {
                        unset($listeners[$key]);
                    }
                }
                $this->listeners = $listeners;
            } else {
                $this->listeners = [];
            }
        }
    }

    /**
     * Gets a sorted list of all listeners.
     *
     * @return array
     */
    public function getListeners()
    {
        $listeners = $this->listeners;

        usort(
            $listeners,
            function ($a, $b) {
                // @codeCoverageIgnoreStart
                if (version_compare(PHP_VERSION, '7.0.0', '<')) {
                    return ($b['priority'] - $a['priority']);
                } else {
                    return ($a['priority'] - $b['priority']);
                }
                // @codeCoverageIgnoreEnd
            }
        );

        return $listeners;
    }

    /**
     * Gets the function.
     *
     * @param mixed $callback Callback
     *
     * @return callable A closure
     *
     * @throws \Exception
     */
    protected function getFunction($callback)
    {
        if (is_callable($callback)) {
            return $callback;
        }

        throw new \Exception('$callback is not a Callable', 1);
    }

    /**
     * Fires a new action.
     *
     * @param string $action Name of action
     * @param array  $args   Arguments passed to the action
     */
    abstract public function fire($action, $args);
}
