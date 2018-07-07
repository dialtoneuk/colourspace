#!/usr/bin/env bash

./cloud_sql_proxy -instances=offmarket-staging:europe-west1:sql-test=tcp:3306 -credential_file=credentials.json