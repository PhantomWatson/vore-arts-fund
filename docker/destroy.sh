#!/bin/bash
set -e

docker-compose down --volumes
docker rmi vore_apache vore_php
