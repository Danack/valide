<?php

declare(strict_types = 1);

namespace Params\ExtractRule;

use Params\DataLocator\InputStorageAye;
use Params\ProcessedValues;
use Params\Rule;
use Params\ValidationResult;

/**
 * The first rule for a parameter. It should extract the initial value
 * out of the Variable Map.
 * @package Params
 */
interface ExtractRule extends Rule
{
    /**
     * @param ProcessedValues $processedValues
     * @param InputStorageAye $dataLocator
     * @return ValidationResult
     */
    public function process(
        ProcessedValues $processedValues,
        InputStorageAye $dataLocator
    ) : ValidationResult;
}
