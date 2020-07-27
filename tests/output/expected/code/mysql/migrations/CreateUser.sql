-- TODO: This SQL will not execute as-is. You will need to generate a strong password and
--       replace <replace_with_strong_password> with your password, surrounded by '
--       You will also need to set this password in the right environment variable and/or file (e.g. .env.local)
-- HINT: You can generate a strong password using an online tool such as: https://passwordsgenerator.net/

-- Create a custom user with full access to the application database locally
-- NOTE: As per MySQL specifications (https://dev.mysql.com/doc/refman/8.0/en/creating-accounts.html):
--       The 'functional_test'@'localhost' account is necessary if there is an anonymous-user account for localhost.
--       Without the 'functional_test'@'localhost' account, that anonymous-user account takes precedence when
--       functional_test connects from the local host and functional_test is treated as an anonymous user.
CREATE USER 'functional_test'@'localhost'
    IDENTIFIED BY <replace_with_strong_password>;
GRANT ALL
    ON code_primer_tests_functional_test.*
    TO 'functional_test'@'localhost';

-- Create a custom user with full access to the application database remotely
CREATE USER 'functional_test'@'%'
    IDENTIFIED BY <replace_with_strong_password>;
GRANT ALL
    ON code_primer_tests_functional_test.*
    TO 'functional_test'@'%';