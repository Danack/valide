<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\InputStorage\InputStorage;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ProcessRule\BoolInput;
use Params\ValidationResult;

/**
 * If a parameter is not set, then the value is the default value.
 */
class GetBoolOrDefault implements ExtractRule
{
    private ?bool $default;

    /**
     * setOrDefaultValidator constructor.
     * @param bool $default The default value to use.
     */
    public function __construct(?bool $default)
    {
        $this->default = $default;
    }

    public function process(
        ProcessedValues $processedValues,
        InputStorage $dataLocator
    ): ValidationResult {
        if ($dataLocator->isValueAvailable() !== true) {
            return ValidationResult::valueResult($this->default);
        }

        $intRule = new BoolInput();

        return $intRule->process(
            $dataLocator->getCurrentValue(),
            $processedValues,
            $dataLocator
        );
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_BOOLEAN);
        $paramDescription->setDefault($this->default);
        $paramDescription->setRequired(false);
    }
}
