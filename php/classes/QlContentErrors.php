<?php

class QlContentErrors
{
    /**
     * @var string[] $errors
     */
    private array $errors = [];

    public function setError(string $error)
    {
        $this->errors[] = $error;
    }

    public function hasErrors(): bool
    {
        return 0 < $this->errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getErrorsAsString(string $delimiter = ';'): string
    {
        return implode($delimiter, $this->errors);
    }
}
