<?php
namespace AppBundle\DQL;

/**
 * DisFunction ::= "DISTANCE" "(" ArithmeticPrimary "," ArithmeticPrimary "," ArithmeticPrimary "," ArithmeticPrimary ")"
 */
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

class Distance extends FunctionNode
{
	public $firstCoordXExpression = null;
	public $secondCoordXExpression = null;
	public $firstCoordYExpression = null;
	public $secondCoordYExpression = null;

	public function parse(\Doctrine\ORM\Query\Parser $parser)
	{
		$parser->match(Lexer::T_IDENTIFIER);
		$parser->match(Lexer::T_OPEN_PARENTHESIS);
		$this->firstCoordXExpression = $parser->ArithmeticPrimary();
		$parser->match(Lexer::T_COMMA);
		$this->secondCoordXExpression = $parser->ArithmeticPrimary();
		$parser->match(Lexer::T_COMMA);
		$this->firstCoordYExpression = $parser->ArithmeticPrimary();
		$parser->match(Lexer::T_COMMA);
		$this->secondCoordYExpression = $parser->ArithmeticPrimary();
		$parser->match(Lexer::T_CLOSE_PARENTHESIS);
	}

	public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
	{
		return 'SQRT(POW(' . $this->firstCoordXExpression->dispatch($sqlWalker) . ' - ' . $this->secondCoordXExpression->dispatch($sqlWalker) .', 2) + ' .
				    'POW(' . $this->firstCoordYExpression->dispatch($sqlWalker) . ' - ' . $this->secondCoordYExpression->dispatch($sqlWalker) .', 2))';
	}
}