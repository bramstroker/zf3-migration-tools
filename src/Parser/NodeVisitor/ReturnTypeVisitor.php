<?php
/**
 * Created by PhpStorm.
 * User: bram
 * Date: 20-3-17
 * Time: 9:29
 */

namespace Stroker\Zf3MigrationTools\Parser\NodeVisitor;


use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeVisitorAbstract;
use Stroker\Zf3MigrationTools\Parser\Exception\ParserException;

class ReturnTypeVisitor extends NodeVisitorAbstract
{
    /** @var array */
    private $assignments = [];

    /** @var string */
    private $returnType = null;

    /**
     * {@inheritdoc}
     */
    public function leaveNode(Node $node) {
        if ($node instanceof Assign) {
            $expr = $node->expr;
            if ($expr instanceof MethodCall) {
                $expr = $expr->var;
            }
            $this->assignments[$node->var->name] = $expr;
        }

        if ($node instanceof Return_) {
            $expression = $node->expr;
            if ($node->expr instanceof Variable) {
                if (!isset($this->assignments[$node->expr->name])) {
                    throw new ParserException('A variable is returned but was never assigned in the method body');
                }
                $expression = $this->assignments[$node->expr->name];
            }
            // @todo, Only classes allowed for now
            if ($expression instanceof New_) {
                $this->returnType = $this->getFqcn($expression);
            }
        }
        return null;
    }

    /**
     * @param New_ $expression
     * @return string
     */
    private function getFqcn(New_ $expression)
    {
        $fqcn = $expression->class;
        if ($expression->class->isFullyQualified()) {
            $fqcn = '\\' . $fqcn;
        }
        return $fqcn;
    }

    /**
     * @return string|null
     */
    public function getReturnType()
    {
        return $this->returnType;
    }
}