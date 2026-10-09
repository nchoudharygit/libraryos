#!/bin/bash

echo "Checking Postgres container..."
docker start libraryos-postgres 2>/dev/null || echo "libraryos-postgres already running or not found — check manually if needed"

mkdir -p logs

echo "Starting Catalog service (8001)..."
(cd services/catalog-service && php artisan serve --port=8001 > ../../logs/catalog.log 2>&1 &)

echo "Starting Consumer service (8002)..."
(cd services/consumer-service && php artisan serve --port=8002 > ../../logs/consumer.log 2>&1 &)

echo "Starting Transaction service (8003)..."
(cd services/transaction-service && php artisan serve --port=8003 > ../../logs/transaction.log 2>&1 &)

echo "Starting Gateway service (8000)..."
(cd services/gateway-service && php artisan serve --port=8000 > ../../logs/gateway.log 2>&1 &)

sleep 1
echo ""
echo "All services launching. Check status with: ps aux | grep 'artisan serve'"
echo "Logs are in the logs/ folder if something fails to start."
