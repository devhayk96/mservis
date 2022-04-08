<?php

namespace App\Exceptions;

/**
 * Holder of all system errors.
 */
class Errors
{
    public const CODE_0400001 = '0400001';
    public const CODE_0400002 = '0400002';
    public const CODE_0400003 = '0400003';
    public const CODE_0400004 = '0400004';
    public const CODE_0400005 = '0400005';
    public const CODE_0400006 = '0400006';
    public const CODE_0400007 = '0400007';

    public const CODE_0401001 = '0401001';

    public const CODE_0403001 = '0403001';

    public const CODE_0404001 = '0404001';

    public const CODE_0500001 = '0500001';
    public const CODE_0500002 = '0500002';

    /**
     * Return list of all errors and their data.
     *
     * @return array
     */
    public static function all(): array
    {
        return [
            self::CODE_0400001 => [
                'message' => 'Amount is not provided.',
                'httpStatusCode' => 400
            ],
            self::CODE_0400006 => [
                'message' => 'Amount should be a valid number.',
                'httpStatusCode' => 400
            ],
            self::CODE_0400007 => [
                'message' => 'Amount should be greater than 0.',
                'httpStatusCode' => 400
            ],
            self::CODE_0400002 => [
                'message' => 'Card number is not provided.',
                'httpStatusCode' => 400
            ],
            self::CODE_0400003 => [
                'message' => 'Token is not provided.',
                'httpStatusCode' => 400
            ],
            self::CODE_0400004 => [
                'message' => 'ID is not provided.',
                'httpStatusCode' => 400
            ],
            self::CODE_0400005 => [
                'message' => 'Transaction has already been created.',
                'httpStatusCode' => 400
            ],
            self::CODE_0401001 => [
                'message' => 'Not valid access.',
                'httpStatusCode' => 401
            ],
            self::CODE_0403001 => [
                'message' => 'Badly formed token.',
                'httpStatusCode' => 403
            ],
            self::CODE_0404001 => [
                'message' => 'Resource does not exist.',
                'httpStatusCode' => 404
            ],
            self::CODE_0500001 => [
                'message' => 'Internal server error.',
                'httpStatusCode' => 500
            ],
            self::CODE_0500002 => [
                'message' => 'Unknown application error.',
                'httpStatusCode' => 500
            ],
        ];
    }
}
