<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Params\InputStorage\ArrayInputStorage;
use Params\ExtractRule\GetIntOrDefault;
use ParamsTest\BaseTestCase;
use Params\ProcessedValues;
use Params\Messages;

/**
 * @coversNothing
 */
class GetIntOrDefaultTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [

//            // Test value is read as string
//            [new ArrayVarMap(['foo' => '5']), 'john', 5],
            // Test value is read as int
            [['foo' => 5], 20, 5],

//            // Test default is used as string
//            [new ArrayVarMap([]), '5', 5],

            // Test default is used as int
            [[], 5, 5],

            // Test default is used as null
            [[], null, null],
        ];
    }

    /**
     * @covers \Params\ExtractRule\GetIntOrDefault
     * @dataProvider provideTestCases
     */
    public function testValidation($data, $default, $expectedValue)
    {
        $rule = new GetIntOrDefault($default);
        $validator = new ProcessedValues();

        $dataStorage = ArrayInputStorage::fromArray($data);
        $dataStorage = $dataStorage->moveKey('foo');

        $validationResult = $rule->process(
            $validator,
            $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    public function provideTestErrorCases()
    {
        yield [null, Messages::INT_REQUIRED_UNSUPPORTED_TYPE];
        yield ['', Messages::INT_REQUIRED_FOUND_EMPTY_STRING];
        yield ['6 apples', Messages::INT_REQUIRED_FOUND_NON_DIGITS2];
        yield ['banana', Messages::INT_REQUIRED_FOUND_NON_DIGITS2];
        yield ['1.1', Messages::INT_REQUIRED_FOUND_NON_DIGITS2];
    }

    /**
     * @covers \Params\ExtractRule\GetIntOrDefault
     * @dataProvider provideTestErrorCases
     */
    public function testErrors($inputValue, $message)
    {
        $default = 5;

        $validator = new ProcessedValues();
        $rule = new GetIntOrDefault($default);
        $validationResult = $rule->process(
            $validator,
            ArrayInputStorage::fromSingleValue('foo', $inputValue)
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            $message,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \Params\ExtractRule\GetIntOrDefault
     */
    public function testDescription()
    {
        $rule = new GetIntOrDefault(4);
        $description = $this->applyRuleToDescription($rule);

        $rule->updateParamDescription($description);
        $this->assertSame('integer', $description->getType());
        $this->assertFalse($description->getRequired());
        $this->assertSame(4, $description->getDefault());
    }
}
