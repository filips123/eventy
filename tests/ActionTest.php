<?php

namespace EventyClassic\Tests;

use PHPUnit\Framework\TestCase;
use EventyClassic\Events;

class ActionTest extends TestCase
{
    public function setUp()
    {
        $this->events = new Events();
    }

    /**
     * @test
     */
    public function testCanHookCallable()
    {
        $this->events->addAction(
            'my_awesome_action',
            function () {
                echo 'Action Fired, Baby!';
            }
        );
        $this->expectOutputString('Action Fired, Baby!');
        $this->events->runAction('my_awesome_action');
    }

    /**
     * @test
     */
    public function testCanHookArray()
    {
        $class = new DummyClass;

        $this->events->addAction('my_amazing_action', [$class, 'write']);
        $this->expectOutputString('Action Fired, Baby!');
        $this->events->runAction('my_amazing_action');
    }

    /**
     * @test
     *
     * @expectedException \Exception
     * @expectedException $callback is not a Callable
     */
    public function testCanNotHookBoolean()
    {
        $this->events->addAction('my_amazing_action', true);
        $this->events->runAction('my_amazing_action');
    }

    /**
     * @test
     */
    public function testHookWithPArameters()
    {
        $this->events->addAction(
            'my_great_action',
            function () {
                echo 'Hello, ' . func_get_args()[0] . '!';
            },
            20
        );

        $this->expectOutputString('Hello, World!');
        $this->events->runAction('my_great_action', 'World');
    }

    /**
     * @test
     */
    public function testHookFiresWhenTwoListnersHaveSamePriority()
    {
        $this->events->addAction(
            'my_great_action',
            function () {
                echo 'Action Fired, Baby!';
            },
            20
        );

        $this->events->addAction(
            'my_great_action',
            function () {
                echo 'Action Fired Again, Baby!';
            },
            20
        );

        $this->expectOutputString('Action Fired, Baby!Action Fired Again, Baby!');

        $this->events->runAction('my_great_action');
    }

    /**
     * @test
     */
    public function testsListnersAreSortedByPriority()
    {
        $this->events->addAction(
            'my_great_action',
            function () {
                echo 'Action Fired, Baby!';
            },
            20
        );

        $this->events->addAction(
            'my_great_action',
            function () {
                echo 'Action Fired, Baby!';
            },
            12
        );

        $this->events->addAction(
            'my_great_action',
            function () {
                echo 'Action Fired, Baby!';
            },
            8
        );

        $this->events->addAction(
            'my_great_action',
            function () {
                echo 'Action Fired, Baby!';
            },
            40
        );

        $this->assertEquals($this->events->getAction()->getListeners()[0]['priority'], 8);
        $this->assertEquals($this->events->getAction()->getListeners()[1]['priority'], 12);
        $this->assertEquals($this->events->getAction()->getListeners()[2]['priority'], 20);
        $this->assertEquals($this->events->getAction()->getListeners()[3]['priority'], 40);
    }

    /**
     * @test
     */
    public function testSingleActionIsRemoved()
    {
        // check the collection has 1 item
        $this->events->addAction('my_great_action', 'my_great_function', 30, 1);
        $this->events->addAction('my_great_action', 'my_great_function', 10, 1);

        $count = 0;
        foreach ($this->events->getAction()->getListeners() as $listner) {
            if ($listner['hook'] == 'my_great_action') {
                $count++;
            }
        }
        $this->assertEquals($count, 2);

        // check removeAction removes the correct action
        $this->events->removeAction('my_great_action', 'my_great_function', 30);

        $count = 0;
        foreach ($this->events->getAction()->getListeners() as $listner) {
            if ($listner['hook'] == 'my_great_action') {
                $count++;
            }
        }
        $this->assertEquals($count, 1);

        // check that the action with priority 10 still exists in the collection
        // (only the action with priority 30 should've been removed)
        $priority = 0;
        foreach ($this->events->getAction()->getListeners() as $listner) {
            if ($listner['hook'] == 'my_great_action') {
                $priority = $listner['priority'];
            }
        }
        $this->assertEquals($priority, 10);
    }

    /**
     * @test
     */
    public function testAllActionsAreRemoved()
    {
        // check the collection has 3 items before checking they're removed
        $this->events->addAction('my_great_action', 'my_great_function', 30, 1);
        $this->events->addAction('my_great_action', 'my_other_great_function', 30, 1);
        $this->events->addAction('my_great_action_2', 'my_great_function', 30, 1);
        $this->assertEquals(count($this->events->getAction()->getListeners()), 3);

        // check removeFilter removes the filter
        $this->events->removeAllActions();
        $this->assertEquals(count($this->events->getAction()->getListeners()), 0);
    }

    /**
     * @test
     */
    public function testAllActionsAreRemovedByHook()
    {
        // check the collection has 3 items before checking they're removed correctly
        $this->events->addAction('my_great_action', 'my_great_function', 30, 1);
        $this->events->addAction('my_great_action', 'my_other_great_function', 30, 1);
        $this->events->addAction('my_great_action_2', 'my_great_function', 30, 1);
        $this->assertEquals(count($this->events->getAction()->getListeners()), 3);

        // check removeAction removes the filter
        $this->events->removeAllActions('my_great_action');

        $count = 0;
        foreach ($this->events->getAction()->getListeners() as $listner) {
            if ($listner['hook'] == 'my_great_action') {
                $count++;
            }
        }
        $this->assertEquals($count, 0);

        // check that the other action wasn't removed
        $count = 0;
        foreach ($this->events->getAction()->getListeners() as $listner) {
            if ($listner['hook'] == 'my_great_action_2') {
                $count++;
            }
        }
        $this->assertEquals($count, 1);
    }
}
