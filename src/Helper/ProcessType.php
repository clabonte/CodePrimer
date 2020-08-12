<?php

namespace CodePrimer\Helper;

/**
 * This interface defines the list of 'native' process types currently supported by CodePrimer.
 */
interface ProcessType
{
    /** @var string Process invoked when the user wants to connect with the application */
    public const LOGIN = 'login';
    /** @var string Process invoked when the user wants to register with the application */
    public const REGISTER = 'register';
    /** @var string Process invoked when the user wants to disconnect from the application */
    public const LOGOUT = 'logout';
    public const CREATE = 'create';
    public const RETRIEVE = 'retrieve';
    public const UPDATE = 'update';
    public const DELETE = 'delete';
    public const CUSTOM = 'custom';
}
