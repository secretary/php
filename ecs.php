<?php

use PhpCsFixer\Fixer\Alias\ModernizeStrposFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use PhpCsFixer\Fixer\Comment\SingleLineCommentStyleFixer;
use PhpCsFixer\Fixer\ControlStructure\{NoUselessElseFixer, YodaStyleFixer};
use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;
use PhpCsFixer\Fixer\FunctionNotation\ReturnTypeDeclarationFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\IsNullFixer;
use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\Fixer\Operator\IncrementStyleFixer;
use PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoPackageFixer;
use PhpCsFixer\Fixer\ReturnNotation\NoUselessReturnFixer;
use PhpCsFixer\Fixer\ReturnNotation\ReturnAssignmentFixer;
use PhpCsFixer\Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

/**
 * @see https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/master/doc/list.rst
 * @see https://github.com/symplify/easy-coding-standard
 */
return function (ECSConfig $ecsConfig): void {
    $ecsConfig->cacheDirectory(__DIR__.'/.ecs_cache');
    $ecsConfig->parallel();

    $ecsConfig->paths([
        __DIR__.'/src',
    ]);

    $ecsConfig->skip([
        IsNullFixer::class,
        MultilineWhitespaceBeforeSemicolonsFixer::class,
        NativeFunctionInvocationFixer::class,
        ReturnAssignmentFixer::class,
        SingleLineCommentStyleFixer::class,
        YodaStyleFixer::class,
        PhpdocNoPackageFixer::class,
    ]);

    $ecsConfig->sets([
        SetList::PSR_12,
    ]);

    $ecsConfig->rules([
        NoUselessReturnFixer::class,
        NoUselessElseFixer::class,
        ModernizeStrposFixer::class,
        NoUnusedImportsFixer::class,
        NoSuperfluousPhpdocTagsFixer::class,
        ReturnTypeDeclarationFixer::class,
    ]);

    $ecsConfig->ruleWithConfiguration(ConcatSpaceFixer::class, [
        'spacing' => 'none',
    ]);

    $ecsConfig->ruleWithConfiguration(IncrementStyleFixer::class, [
        'style' => 'post'
    ]);

    $ecsConfig->ruleWithConfiguration(ClassAttributesSeparationFixer::class, [
        'elements' => ['const' => 'only_if_meta', 'method' => 'one', 'property' => 'one', 'trait_import' => 'only_if_meta'],
    ]);


    $ecsConfig->ruleWithConfiguration(BlankLineBeforeStatementFixer::class, [
        'statements' => ['if', 'break', 'continue', 'declare', 'return', 'throw', 'try', 'switch'],
    ]);

    $ecsConfig->ruleWithConfiguration(BinaryOperatorSpacesFixer::class, [
        'default'   => 'align_single_space_minimal',
        'operators' => [
            '|'  => 'no_space',
            '/' => null,
            '*' => null,
            '||' => null,
            '&&' => null,
        ],
    ]);

    $header = <<<'EOF'
@author    Aaron Scherer <aequasi@gmail.com>
@date      2019
@license   https://opensource.org/licenses/MIT
EOF;

    $ecsConfig->ruleWithConfiguration(HeaderCommentFixer::class, [
        'header' => $header
    ]);
};
