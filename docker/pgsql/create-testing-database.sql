SELECT 'CREATE DATABASE merchanto_eshop_testing'
WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'merchanto_eshop_testing')\gexec
