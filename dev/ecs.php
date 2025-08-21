<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\FunctionSpacingSniff;
use PhpCsFixer\Fixer\Casing\IntegerLiteralCaseFixer;
use PhpCsFixer\Fixer\CastNotation\NoShortBoolCastFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer;
use PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer;
use PhpCsFixer\Fixer\Comment\SingleLineCommentSpacingFixer;
use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer;
use PhpCsFixer\Fixer\LanguageConstruct\CombineConsecutiveIssetsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\SingleSpaceAfterConstructFixer;
use PhpCsFixer\Fixer\Operator\OperatorLinebreakFixer;
use PhpCsFixer\Fixer\Operator\TernaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\StringNotation\SimpleToComplexStringVariableFixer;
use PhpCsFixer\Fixer\Whitespace\ArrayIndentationFixer;
use PhpCsFixer\Fixer\Whitespace\MethodChainingIndentationFixer;
use PhpCsFixer\Fixer\Whitespace\NoSpacesAroundOffsetFixer;
use PhpCsFixer\Fixer\Whitespace\TypeDeclarationSpacesFixer;
use PhpCsFixer\Fixer\Whitespace\TypesSpacesFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

if (!defined('REPOSITORY_ROOT')) {
    define('REPOSITORY_ROOT', __DIR__ . '/..');
}

return static function (ECSConfig $config): void {
    $config->parallel();
    $config->lineEnding("\n");

    $config->paths([
        REPOSITORY_ROOT . '/src',
        REPOSITORY_ROOT . '/tests',
    ]);

    $config->sets([
        SetList::CLEAN_CODE,
        SetList::PSR_12,
    ]);

    $config->skip([
        REPOSITORY_ROOT . '/vendor',

        // Checks
        ProtectedToPrivateFixer::class => null, // This is a weird rule that breaks SOLID principles and class inheritance design
    ]);

    // Override settings or add rules not used in above sets
    $config->rule(ArrayIndentationFixer::class);
    $config->ruleWithConfiguration(ClassDefinitionFixer::class, [
        'inline_constructor_arguments' => false,
        'multi_line_extends_each_single_line' => true,
    ]);
    $config->rule(CombineConsecutiveIssetsFixer::class);
    $config->ruleWithConfiguration(ForbiddenFunctionsSniff::class, [
        'forbiddenFunctions' => [
            'create_function' => null,
            'dd' => null,
            'delete' => 'unset',
            'die' => null,
            'eval' => null,
            'print' => 'echo',
            'sizeof' => 'count',
            'var_dump' => null,
            'dump' => null,
        ],
    ]);
    $config->ruleWithConfiguration(FunctionSpacingSniff::class, [
        'spacing' => 1,
        'spacingAfterLast' => 0,
        'spacingBeforeFirst' => 0,
    ]);
    $config->rule(IntegerLiteralCaseFixer::class);
    $config->ruleWithConfiguration(LineLengthSniff::class, [
        'absoluteLineLimit' => 200,
        'lineLimit' => 160,
    ]);
    $config->rule(MethodArgumentSpaceFixer::class);
    $config->rule(MethodChainingIndentationFixer::class);
    $config->ruleWithConfiguration(MultilineWhitespaceBeforeSemicolonsFixer::class, [
        'strategy' => MultilineWhitespaceBeforeSemicolonsFixer::STRATEGY_NEW_LINE_FOR_CHAINED_CALLS,
    ]);
    $config->rule(NoShortBoolCastFixer::class);
    $config->rule(NoSpacesAroundOffsetFixer::class);
    $config->rule(OperatorLinebreakFixer::class);
    $config->rule(SimpleToComplexStringVariableFixer::class);
    $config->rule(SingleLineCommentSpacingFixer::class);
    $config->rule(SingleSpaceAfterConstructFixer::class);
    $config->ruleWithConfiguration(TrailingCommaInMultilineFixer::class, [
        'elements' => ['arguments', 'arrays', 'match', 'parameters'],
    ]);
    $config->rule(TypeDeclarationSpacesFixer::class);
    $config->ruleWithConfiguration(TypesSpacesFixer::class, [
        'space' => 'single',
    ]);
};
