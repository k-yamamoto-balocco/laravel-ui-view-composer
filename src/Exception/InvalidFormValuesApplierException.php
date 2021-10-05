<?php

namespace GitBalocco\LaravelUiViewComposer\Exception;

use InvalidArgumentException;
use Throwable;

class InvalidFormValuesApplierException extends InvalidArgumentException
{
    /**
     * InvalidFormValuesApplierException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        if (!$message) {
            $message = 'FormValueApplier インターフェースを実装したインスタンスのみset可能です。';
        }
        parent::__construct($message, $code, $previous);
    }
}
