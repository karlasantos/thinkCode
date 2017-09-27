<?php
/**
 * XC ERP
 * @copyright Copyright (c) XC Ltda
 * @author Wagner Silveira <wagnerdevel@gmail.com>
 */
namespace Core\Model;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

/**
 * User Defined Function
 *
 * "YEAR" "(" SimpleArithmeticExpression ")"
 */
class YearFunction extends FunctionNode
{
    /**
     * @var \Doctrine\ORM\Query\AST\SimpleArithmeticExpression
     */
    public $simpleArithmeticExpression;

    /**
     * @override
     */
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'EXTRACT(YEAR FROM ' . $sqlWalker->walkSimpleArithmeticExpression(
            $this->simpleArithmeticExpression
        ) . ')';
    }

    /**
     * @override
     */
    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->simpleArithmeticExpression = $parser->SimpleArithmeticExpression();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
