<?php


namespace CodePrimer\Helper {


    interface FieldType
    {
        public const UUID = 'uuid';
        public const STRING = 'string';
        public const TEXT = 'text';
        public const EMAIL = 'email';
        public const URL = 'url';
        public const PASSWORD = 'password';
        public const PHONE = 'phone';
        public const DATE = 'date';
        public const TIME = 'time';
        public const DATETIME = 'datetime';
        public const BOOL = 'bool';
        public const BOOLEAN = 'boolean';
        public const INT = 'int';
        public const INTEGER = 'integer';
        public const ID = 'id';
        public const LONG = 'long';
        public const FLOAT = 'float';
        public const DOUBLE = 'double';
        public const DECIMAL = 'decimal';
        public const PRICE = 'price';
        public const RANDOM_STRING = 'randomstring';
    }
}
