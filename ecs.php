<?php

use PhpCsFixer\Fixer\Alias\ModernizeStrposFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use PhpCsFixer\Fixer\Comment\SingleLineCommentStyleFixer;
use PhpCsFixer\Fixer\ControlStructure\{NoUselessElseFixer, YodaStyleFixer};
use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;
use PhpCsFixer\Fixer\LanguageConstruct\IsNullFixer;
use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Operator\IncrementStyleFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoPackageFixer;
use PhpCsFixer\Fixer\ReturnNotation\NoUselessReturnFixer;
use PhpCsFixer\Fixer\ReturnNotation\ReturnAssignmentFixer;
use PhpCsFixer\Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

/**
 * @see https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/master/doc/list.rst
 * @see https://github.com/symplify/easy-coding-standard
 */
return function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::CACHE_DIRECTORY, __DIR__.'/.ecs_cache');
    $parameters->set(Option::PATHS, [
        __DIR__.'/src',
    ]);
    $parameters->set(Option::SKIP, [
        IsNullFixer::class,
        MultilineWhitespaceBeforeSemicolonsFixer::class,
        NativeFunctionInvocationFixer::class,
        ReturnAssignmentFixer::class,
        SingleLineCommentStyleFixer::class,
        YodaStyleFixer::class,
        PhpdocNoPackageFixer::class,
    ]);

    $containerConfigurator->import(SetList::PSR_12);
    $containerConfigurator->import(SetList::SYMFONY);
    $containerConfigurator->import(SetList::PHP_CS_FIXER);
    $containerConfigurator->import(SetList::SYMFONY_RISKY);

    $services = $containerConfigurator->services();
    $services->set(NoUselessReturnFixer::class);
    $services->set(NoUselessElseFixer::class);
    $services->set(ModernizeStrposFixer::class);
    $services->set(IncrementStyleFixer::class)
        ->call('configure', [[
            'style' => 'post',
        ]]);

    $services->set(ClassAttributesSeparationFixer::class)
        ->call('configure', [[
            'elements' => ['const' => 'only_if_meta', 'method' => 'one', 'property' => 'one', 'trait_import' => 'only_if_meta'],
        ]]);

    $services->set(BlankLineBeforeStatementFixer::class)
        ->call('configure', [[
            'statements' => ['if', 'break', 'continue', 'declare', 'return', 'throw', 'try', 'switch'],
        ]]);

    $services->set(BinaryOperatorSpacesFixer::class)
        ->call('configure', [[
            'default'   => 'align_single_space_minimal',
            'operators' => [
                '|'  => 'no_space',
                '/' => null,
                '*' => null,
                '||' => null,
                '&&' => null,
            ],
        ]]);

    $header = <<<'EOF'
@author    Aaron Scherer <aequasi@gmail.com>
@date      2019
@license   https://opensource.org/licenses/MIT
EOF;

    $services->set(HeaderCommentFixer::class)
        ->call('configure', [
            ['header' => $header,
            ]]);
};
