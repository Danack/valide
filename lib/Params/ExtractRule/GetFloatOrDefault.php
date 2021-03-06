<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\InputStorage\InputStorage;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ProcessRule\FloatInput;
use Params\ValidationResult;

class GetFloatOrDefault implements ExtractRule
{
    private ?float $default;

    /**
     * @param float $default
     */
    public function __construct(?float  $default)
    {
        $this->default = $default;
    }

    public function process(
        ProcessedValues $processedValues,
        InputStorage $dataLocator
    ) : ValidationResult {

        if ($dataLocator->isValueAvailable() !== true) {
            return ValidationResult::valueResult($this->default);
        }

        $floatInput = new FloatInput();

        return $floatInput->process(
            $dataLocator->getCurrentValue(),
            $processedValues,
            $dataLocator
        );
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_NUMBER);
        $paramDescription->setDefault($this->default);
        $paramDescription->setRequired(false);
    }
}
