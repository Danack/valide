<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Params\Messages;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetString;
use Params\ProcessedValues;
use Params\InputStorage\ArrayInputStorage;

/**
 * @coversNothing
 */
class GetStringTest extends BaseTestCase
{
    /**
     * @covers \Params\ExtractRule\GetString
     */
    public function testMissingGivesError()
    {
        $rule = new GetString();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            ArrayInputStorage::createMissing('foo')
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::VALUE_NOT_SET,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \Params\ExtractRule\GetString
     */
    public function testValidation()
    {
        $expectedValue = 'John';

        $rule = new GetString();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            ArrayInputStorage::fromArraySetFirstValue([$expectedValue])
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    /**
     * @covers \Params\ExtractRule\GetString
     */
    public function testFromArrayErrors()
    {
        $index = 'foo';

        $data = [$index => [1, 2, 3]];

        $rule = new GetString();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            ArrayInputStorage::fromArraySetFirstValue($data)
        );

        $this->assertValidationProblemRegexp(
            '/' . $index,
            Messages::STRING_REQUIRED_FOUND_NON_SCALAR,
            $validationResult->getValidationProblems()
        );
    }


    /**
     * @covers \Params\ExtractRule\GetString
     */
    public function testFromObjectErrors()
    {
        $index = 'foo';

        $data = [$index => new \StdClass()];

        $rule = new GetString();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            ArrayInputStorage::fromArraySetFirstValue($data)
        );

        $this->assertValidationProblemRegexp(
            '/' . $index,
            Messages::STRING_REQUIRED_FOUND_NON_SCALAR,
            $validationResult->getValidationProblems()
        );
    }


    /**
     * @covers \Params\ExtractRule\GetString
     */
    public function testDescription()
    {
        $rule = new GetString();
        $description = $this->applyRuleToDescription($rule);

        $rule->updateParamDescription($description);
        $this->assertSame('string', $description->getType());
        $this->assertTrue($description->getRequired());
    }
}
