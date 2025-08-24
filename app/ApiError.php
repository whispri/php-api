<?php

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;

const OTP_INVALID_ERROR = 'OTP_INVALID_ERROR';
const PASSWORD_INVALID_ERROR = 'PASSWORD_INVALID_ERROR';
const INTERNAL_SERVER_ERROR = 'INTERNAL_SERVER_ERROR';
const PHONE_NOT_FOUND_ERROR = 'PHONE_NOT_FOUND_ERROR';
const PHONE_ALREADY_EXITS = 'PHONE_ALREADY_EXITS';

function getHttpStatusCode(Throwable $exception): int
{
    if ($exception instanceof HttpException) {
        return $exception->getStatusCode();
    } elseif ($exception instanceof ValidationException) {
        return 422;
    } elseif ($exception instanceof AuthenticationException) {
        return 401;
    } elseif ($exception instanceof NotFoundHttpException) {
        return 404;
    }
    return 500;
}
