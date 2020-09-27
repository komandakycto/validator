<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\HasValidationErrorMessage;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\DataSetInterface;

/**
 * StringValidator validates that the attribute value is of certain length.
 *
 * Note, this validator should only be used with string-typed attributes.
 */
class HasLength extends Rule
{
    use HasValidationErrorMessage;

    /**
     * @var int|null maximum length. null means no maximum length limit.
     * @see tooLongMessage for the customized message for a too long string.
     */
    private ?int $max = null;
    /**
     * @var int|null minimum length. null means no minimum length limit.
     * @see tooShortMessage for the customized message for a too short string.
     */
    private ?int $min = null;
    /**
     * @var string user-defined error message used when the value is not a string.
     */
    private string $message = 'This value must be a string.';
    /**
     * @var string user-defined error message used when the length of the value is smaller than {@see $min}.
     */
    private string $tooShortMessage = 'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.';
    /**
     * @var string user-defined error message used when the length of the value is greater than {@see $max}.
     */
    private string $tooLongMessage = 'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.';
    /**
     * @var string the encoding of the string value to be validated (e.g. 'UTF-8').
     * If this property is not set, application wide encoding will be used.
     */
    protected string $encoding = 'UTF-8';

    protected function validateValue($value, DataSetInterface $dataSet = null): Error
    {
        $result = new Error();

        if (!is_string($value)) {
            $result->addError($this->message);
            return $result;
        }

        $length = mb_strlen($value, $this->encoding);

        if ($this->min !== null && $length < $this->min) {
            $result->addError($this->tooShortMessage, ['min' => $this->min]);
        }
        if ($this->max !== null && $length > $this->max) {
            $result->addError($this->tooLongMessage, ['max' => $this->max]);
        }

        return $result;
    }

    public function min(int $value): self
    {
        $new = clone $this;
        $new->min = $value;
        return $new;
    }

    public function max(int $value): self
    {
        $new = clone $this;
        $new->max = $value;
        return $new;
    }

    public function encoding(string $encoding): self
    {
        $new = clone $this;
        $new->encoding = $encoding;
        return $new;
    }

    public function tooShortMessage(string $message): self
    {
        $new = clone $this;
        $new->tooShortMessage = $message;
        return $new;
    }

    public function tooLongMessage(string $message): self
    {
        $new = clone $this;
        $new->tooLongMessage = $message;
        return $new;
    }

    public function getName(): string
    {
        return 'hasLength';
    }

    public function getOptions(): array
    {
        return array_merge(
            parent::getOptions(),
            [
                'message' => $this->message,
                'min' => $this->min,
                'tooShortMessage' => $this->tooShortMessage,
                'max' => $this->max,
                'tooLongMessage' => $this->tooLongMessage,
                'encoding' => $this->encoding,
            ],
        );
    }
}
