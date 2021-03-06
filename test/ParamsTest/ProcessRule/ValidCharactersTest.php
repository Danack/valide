<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\InputStorage\ArrayInputStorage;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\ValidCharacters;
use Params\ProcessRule\SaneCharacters;
use Params\ProcessedValues;

/**
 * @coversNothing
 */
class ValidCharactersTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            ['a-zA-Z', 'john', null],
            ['a-zA-Z', 'johnny-5', 6],  // bad digit and hyphen
            ['a-zA-Z', 'jo  hn', 2], // bad space

            [implode(SaneCharacters::ALLOWED_CHAR_TYPES), "jo.hn", null], //punctuation is not letter or number
        ];
    }

    /**
     * @dataProvider provideTestCases
     * @covers \Params\ProcessRule\ValidCharacters
     */
    public function testValidation($validCharactersPattern, $testValue, $expectedErrorPosition)
    {
        $rule = new ValidCharacters($validCharactersPattern);
        $processedValues = new ProcessedValues();
        $dataLocator = ArrayInputStorage::fromSingleValue('foo', $testValue);

        $validationResult = $rule->process(
            $testValue, $processedValues, $dataLocator
        );
        if ($expectedErrorPosition !== null) {
            $this->assertValidationProblemRegexp(
                '/foo',
                \Params\Messages::STRING_FOUND_INVALID_CHAR,
                $validationResult->getValidationProblems()
            );

            $this->assertValidationProblemRegexp(
                '/foo',
                $validCharactersPattern,
                $validationResult->getValidationProblems()
            );

            // Check the correct position is in the error message.
            $this->assertCount(1, $validationResult->getValidationProblems());
            $validationProblem = $validationResult->getValidationProblems()[0];
            $this->assertStringContainsString(
                (string)$expectedErrorPosition,
                $validationProblem->getProblemMessage()
            );
        }
        else {
            $this->assertNoProblems($validationResult);
        }
    }
}
