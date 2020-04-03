<?php

declare(strict_types = 1);

namespace Params\ExtractRule;

use Params\Messages;
use Params\Param;
use VarMap\ArrayVarMap;
use VarMap\VarMap;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamValues;
use Params\Path;
use function Params\getInputParameterListForClass;
use function Params\createArrayForTypeWithRules;

class GetArrayOfType implements ExtractRule
{
    /** @var class-string */
    private string $className;

    /** @var \Params\Param[] */
    private array $inputParameterList;

    /**
     * @param class-string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
        $this->inputParameterList = getInputParameterListForClass($this->className);
    }

    public function process(
        Path $path,
        VarMap $varMap,
        ParamValues $paramValues
    ): ValidationResult {

        // Check its set
        if ($varMap->has($path->getCurrentName()) !== true) {
            return ValidationResult::errorResult($path, Messages::ERROR_MESSAGE_NOT_SET_VARIANT_1);
        }

        // Check its an array
        $itemData = $varMap->get($path->getCurrentName());
        if (is_array($itemData) !== true) {
            return ValidationResult::errorResult($path, Messages::ERROR_MESSAGE_NOT_ARRAY_VARIANT_1);
        }

        return createArrayForTypeWithRules(
            $path,
            $this->className,
            $itemData,
            $this->inputParameterList
        );
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // TODO - implement
    }
}
