#!/bin/bash
set -e
psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" <<-EOSQL
    CREATE DATABASE catalog_db;
    CREATE DATABASE consumer_db;
    CREATE DATABASE transaction_db;
    CREATE DATABASE gateway_db;
EOSQL