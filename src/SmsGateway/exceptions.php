<?php
namespace ProfiCloS\SmsGateway;

class Exception extends \Exception { }

class RuntimeException extends Exception { }

class InvalidArgumentException extends Exception { }

class AuthenticationException extends InvalidArgumentException { }

class CreditException extends RuntimeException { }

class ServerException extends RuntimeException { }
