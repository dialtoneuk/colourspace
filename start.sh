#!/usr/bin/env bash

./cloud_sql_proxy -instances=colourspace-209220:us-central1:colourspace-us=tcp:3306 -credential_file=credentials.json