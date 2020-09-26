<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\Rule\Callback;

/**
 * Rules represents multiple rules for a single value
 */
final class Rules implements RuleInterface
{
    use SkippableTrait, ValueTrait;

    /**
     * @var Rule[]
     */
    private array $rules = [];

    public function __construct(iterable $rules = [])
    {
        $this->resetSkipOptions();
        foreach ($rules as $rule) {
            $this->add($rule);
        }
    }

    /**
     * @param Rule|callable
     */
    public function add($rule): void
    {
        $this->rules[] = $this->normalizeRule($rule);
    }

    public function validate($value, DataSetInterface $dataSet = null, bool $previousRulesErrored = false): Result
    {
        $compoundResult = new Result();
        foreach ($this->rules as $rule) {
            $ruleResult = $this->mutateRule($rule)->validate($value, $dataSet, $previousRulesErrored);
            if ($ruleResult->isValid() === false) {
                $previousRulesErrored = true;
                foreach ($ruleResult->getErrors() as $message) {
                    $compoundResult->addError($message);
                }
            }
        }
        return $compoundResult;
    }

    private function normalizeRule($rule): Rule
    {
        if (is_callable($rule)) {
            $rule = new Callback($rule);
        }

        if (!$rule instanceof Rule) {
            throw new \InvalidArgumentException('Rule should be either instance of Rule class or a callable');
        }

        return $rule;
    }

    /**
     * Apply composite options to each rule in composite.
     * @param Rule $rule
     * @return Rule
     */
    private function mutateRule(Rule $rule)
    {
        if ($this->skipOnError !== null) {
            $rule = $rule->skipOnError($this->skipOnError);
        }

        if ($this->skipOnEmpty !== null) {
            $rule = $rule->skipOnEmpty($this->skipOnEmpty);
        }

        return $rule;
    }

    private function resetSkipOptions(): void
    {
        $this->skipOnEmpty = null;
        $this->skipOnError = null;
    }

    /**
     * Return rules as array.
     * @return array
     */
    public function asArray(): array
    {
        $arrayOfRules = [];
        foreach ($this->rules as $rule) {
            $arrayOfRules[] = array_merge([$rule->getName()], $rule->getOptions());
        }
        return $arrayOfRules;
    }
}
