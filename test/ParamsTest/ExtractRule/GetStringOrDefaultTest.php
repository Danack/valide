<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Params\DataLocator\DataStorage;
use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetStringOrDefault;
use Params\ProcessedValues;
use Params\DataLocator\NotAvailableInputStorageAye;

/**
 * @coversNothing
 */
class GetStringOrDefaultTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            [new ArrayVarMap(['foo' => 'bar']), 'john', 'bar'],
            [new ArrayVarMap([]), 'john', 'john'],


//            [new ArrayVarMap([]), null, null],
        ];
    }

    /**
     * @covers \Params\ExtractRule\GetStringOrDefault
     */
    public function testValidation()
    {
        $default = 'bar';

        $rule = new GetStringOrDefault($default);
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator, DataStorage::fromArraySetFirstValue(['John'])
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), 'John');
    }

    /**
     * @covers \Params\ExtractRule\GetStringOrDefault
     */
    public function testValidationForMissing()
    {
        $default = 'bar';

        $rule = new GetStringOrDefault($default);
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator, new NotAvailableInputStorageAye()
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $default);
    }

    /**
     * @covers \Params\ExtractRule\GetStringOrDefault
     */
    public function testDescription()
    {
        $rule = new GetStringOrDefault('John');
        $description = $this->applyRuleToDescription($rule);

        $rule->updateParamDescription($description);
        $this->assertSame('string', $description->getType());
        $this->assertFalse($description->getRequired());
        $this->assertSame('John', $description->getDefault());
    }
}
