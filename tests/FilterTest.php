<?php

namespace EventyTests\Unit;

use EventyTests\DummyClass;
use PHPUnit\Framework\TestCase;
use EventyClassic\Events;

class FilterTest extends TestCase
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
        $this->events->addFilter('my_awesome_filter', function ($value) {
            return $value.' Filtered';
        });
        $this->assertEquals($this->events->runFilter('my_awesome_filter', 'Value Was'), 'Value Was Filtered');
    }

    /**
     * @test
     */
    public function testCanHookArray()
    {
        $class = new class('DummyClass') {
            public function filter($value)
            {
                return $value.' Filtered';
            }
        };
        $this->events->addFilter('my_amazing_filter', [$class, 'filter']);

        $this->assertEquals($this->events->runFilter('my_amazing_filter', 'Value Was'), 'Value Was Filtered');
    }

    /**
     * @test
     *
     * @expectedException \Exception
     * @expectedException $callback is not a Callable
     */
    public function testCanNotHookBoolean()
    {
        $this->events->addFilter('my_amazing_filter', true);
        $this->events->runFilter('my_amazing_filter', 'Value Was');
    }

    /**
     * @test
     */
    public function testHookFiresWhenTwoListnersHaveSamePriority()
    {
        $this->events->addFilter('my_great_filter', function ($value) {
            return $value.' Once';
        }, 20);

        $this->events->addFilter('my_great_filter', function ($value) {
            return $value.' And Twice';
        }, 20);

        $this->assertEquals($this->events->runFilter('my_great_filter', 'I Was Filtered'), 'I Was Filtered Once And Twice');
    }

    /**
     * @test
     */
    public function testsListnersAreSortedByPriority()
    {
        $this->events->addFilter('my_awesome_filter', function ($value) {
            return $value.' Filtered';
        }, 20);

        $this->events->addFilter('my_awesome_filter', function ($value) {
            return $value.' Filtered';
        }, 8);

        $this->events->addFilter('my_awesome_filter', function ($value) {
            return $value.' Filtered';
        }, 12);

        $this->events->addFilter('my_awesome_filter', function ($value) {
            return $value.' Filtered';
        }, 40);

        $this->assertEquals($this->events->getFilter()->getListeners()[0]['priority'], 8);
        $this->assertEquals($this->events->getFilter()->getListeners()[1]['priority'], 12);
        $this->assertEquals($this->events->getFilter()->getListeners()[2]['priority'], 20);
        $this->assertEquals($this->events->getFilter()->getListeners()[3]['priority'], 40);
    }

    /**
     * @test
     */
    public function testSingleFilterIsRemoved()
    {
        // check the collection has 1 item
        $this->events->addFilter('my_awesome_filter', 'my_awesome_function', 30, 1);

        $count = 0;
        foreach ($this->events->getFilter()->getListeners() as $listner) {
            if ($listner['hook'] == 'my_awesome_filter') {
                $count++;
            }
        }
        $this->assertEquals($count, 1);

        // check removeFilter removes the filter
        $this->events->removeFilter('my_awesome_filter', 'my_awesome_function', 30);

        $count = 0;
        foreach ($this->events->getFilter()->getListeners() as $listner) {
            if ($listner['hook'] == 'my_awesome_filter') {
                $count++;
            }
        }
        $this->assertEquals($count, 0);
    }

    /**
     * @test
     */
    public function testAllFiltersAreRemoved()
    {
        // check the collection has 3 items before checking they're removed
        $this->events->addFilter('my_awesome_filter', 'my_awesome_function', 30, 1);
        $this->events->addFilter('my_awesome_filter', 'my_other_awesome_function', 30, 1);
        $this->events->addFilter('my_awesome_filter_2', 'my_awesome_function_2', 30, 1);
        $this->assertEquals(count($this->events->getFilter()->getListeners()), 3);

        // check removeFilter removes the filter
        $this->events->removeAllFilters();
        $this->assertEquals(count($this->events->getFilter()->getListeners()), 0);
    }

    /**
     * @test
     */
    public function testAllFiltersAreRemovedByHook()
    {
        // check the collection has 1 item
        $this->events->addFilter('my_awesome_filter', 'my_awesome_function', 30, 1);
        $this->events->addFilter('my_awesome_filter', 'my_other_awesome_function', 30, 1);
        $this->events->addFilter('my_awesome_filter_2', 'my_awesome_function', 30, 1);
        $this->assertEquals(count($this->events->getFilter()->getListeners()), 3);

        // check removeFilter removes the filter
        $this->events->removeAllFilters('my_awesome_filter');

        $count = 0;
        foreach ($this->events->getFilter()->getListeners() as $listner) {
            if ($listner['hook'] == 'my_awesome_filter') {
                $count++;
            }
        }
        $this->assertEquals($count, 0);

        // check that the other filter wasn't removed
        $count = 0;
        foreach ($this->events->getFilter()->getListeners() as $listner) {
            if ($listner['hook'] == 'my_awesome_filter_2') {
                $count++;
            }
        }
        $this->assertEquals($count, 1);
    }
}
