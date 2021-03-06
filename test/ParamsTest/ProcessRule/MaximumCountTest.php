<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\Messages;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\MaximumCount;
use Params\Exception\LogicException;
use Params\ProcessedValues;
use Params\InputStorage\ArrayInputStorage;
use function \Danack\PHPUnitHelper\templateStringToRegExp;

/**
 * @coversNothing
 */
class MaximumCountTest extends BaseTestCase
{
    public function provideWorksCases()
    {
        return [
            [3, []], // 3 <= 3
            [3, [1, 2, 3]], // 3 <= 3
            [4, [1, 2, 3]], // 3 <= 4
        ];
    }

    /**
     * @dataProvider provideWorksCases
     * @covers \Params\ProcessRule\MaximumCount
     */
    public function testWorks(int $maximumCount, $values)
    {
        $rule = new MaximumCount($maximumCount);
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $values, $processedValues, ArrayInputStorage::fromArray([$values])
        );
        $this->assertNoProblems($validationResult);
        $this->assertFalse($validationResult->isFinalResult());
        $this->assertSame($values, $validationResult->getValue());
    }

    public function provideFailsCases()
    {
        return [
            [0, [1, 2, 3]], // 3 > 0
            [3, [1, 2, 3, 4]], // 4 > 3
        ];
    }

    /**
     * @dataProvider provideFailsCases
     * @covers \Params\ProcessRule\MaximumCount
     */
    public function testFails(int $maximumCount, $values)
    {
        $rule = new MaximumCount($maximumCount);
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $values, $processedValues, ArrayInputStorage::fromArray([$values])
        );
        $this->assertNull($validationResult->getValue());
        $this->assertTrue($validationResult->isFinalResult());

//        'Number of elements in foo is too large. Max allowed is 0 but got 3.'

//        $this->assertRegExp(
//            stringToRegexp(MaximumCount::ERROR_TOO_MANY_ELEMENTS),
//            $validationResult->getValidationProblems()['/foo']
//        );

        $this->assertCount(1, $validationResult->getValidationProblems());
        $this->assertValidationProblemRegexp(
            '/',
            Messages::ERROR_TOO_MANY_ELEMENTS,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \Params\ProcessRule\MaximumCount
     */
    public function testMinimimCountZero()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(Messages::ERROR_MAXIMUM_COUNT_MINIMUM);
        new MaximumCount(-2);
    }

    /**
     * @covers \Params\ProcessRule\MaximumCount
     */
    public function testInvalidOperand()
    {
        $rule = new MaximumCount(3);
        $this->expectException(LogicException::class);

        $processedValues = new ProcessedValues();
        $this->expectErrorMessageMatches(
            templateStringToRegExp(Messages::ERROR_WRONG_TYPE_VARIANT_1)
        );

        $dataLocator = ArrayInputStorage::fromArraySetFirstValue([]);

        $rule->process(
            'a banana', $processedValues, $dataLocator
        );
    }

    /**
     * @covers \Params\ProcessRule\MaximumCount
     */
    public function testDescription()
    {
        $rule = new MaximumCount(3);
        $description = $this->applyRuleToDescription($rule);
        $this->assertSame(3, $description->getMinItems());
    }
}
