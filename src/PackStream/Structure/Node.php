<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) Graph Aware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt\PackStream\Structure;

class Node extends AbstractElement
{
    /**
     * @var \GraphAware\Bolt\PackStream\Structure\TextElement
     */
    protected $identity;

    /**
     * @var \GraphAware\Bolt\PackStream\Structure\ListCollection
     */
    protected $labels;

    /**
     * @var \GraphAware\Bolt\PackStream\Structure\Map
     */
    protected $properties;

    /**
     * @param \GraphAware\Bolt\PackStream\Structure\TextElement $identity
     * @param \GraphAware\Bolt\PackStream\Structure\ListCollection $labels
     * @param \GraphAware\Bolt\PackStream\Structure\Map $properties
     */
    public function __construct(SimpleElement $identity, ListCollection $labels, Map $properties)
    {
        $this->identity = $identity;
        $this->labels = $labels;
        $this->properties = $properties;
    }

    /**
     * @return TextElement
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @return ListCollection
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @return Map
     */
    public function getProperties()
    {
        return $this->properties;
    }

    public function getValue()
    {
        return $this;
    }

    public function __toString()
    {
        $str = '';
        $str .= $this->identity->getValue() . ', ';
        $str .= 'labels : ' . json_encode($this->getLabels()->toArray()) . ', properties : ' . json_encode($this->getProperties()->toArray());

        return $str;
    }
}