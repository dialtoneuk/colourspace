#!/usr/bin/env bash
wget https://dl.google.com/cloudsql/cloud_sql_proxy.linux.386
mv cloud_sql_proxy.linux.386 cloud_sql_proxy
chmod +x cloud_sql_proxy

cloud_sql_proxy -instances=offmarket-staging:europe-west1:sql-test=tcp:3306 -credential_file=credentials.json