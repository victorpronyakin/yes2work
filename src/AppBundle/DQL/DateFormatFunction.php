<?php
/**
 * Created by PhpStorm.
 * Date: 11.04.18
 * Time: 16:23
 */

namespace AppBundle\DQL;


use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\Parser;

class DateFormatFunction extends FunctionNode
{

    protected $dateExpression;

    protected $formatChar;

    public function getSql( SqlWalker $sqlWalker )
    {
        return 'DATE_FORMAT (' . $sqlWalker->walkArithmeticExpression( $this->dateExpression ) . ',' . $sqlWalker->walkStringPrimary( $this->formatChar ) . ')';
    }

    public function parse( Parser $parser )
    {

        $parser->match( Lexer::T_IDENTIFIER );
        $parser->match( Lexer::T_OPEN_PARENTHESIS );

        $this->dateExpression = $parser->ArithmeticExpression();
        $parser->match( Lexer::T_COMMA );

        $this->formatChar = $parser->ArithmeticExpression();

        $parser->match( Lexer::T_CLOSE_PARENTHESIS );
    }
}