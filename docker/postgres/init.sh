#!/bin/bash
set -e

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" <<-EOSQL
    CREATE DATABASE catalog_db;
    CREATE DATABASE inventory_db;
    CREATE DATABASE consumer_db;
    CREATE DATABASE analytics_db;
EOSQL