<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 * (c) Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Twig\Compiler;
use Twig\Node\Node;

/**
 * Represents a block node.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Twig_Node_Block extends Node
{
    public function __construct($name, Twig_NodeInterface $body, $lineno, $tag = null)
    {
        parent::__construct(['body' => $body], ['name' => $name], $lineno, $tag);
    }

    public function compile(Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write(sprintf("public function block_%s(\$context, array \$blocks = [])\n", $this->getAttribute('name')), "{\n")
            ->indent()
        ;

        $compiler
            ->subcompile($this->getNode('body'))
            ->outdent()
            ->write("}\n\n")
        ;
    }
}

class_alias('Twig_Node_Block', 'Twig\Node\BlockNode', false);
