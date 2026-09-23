#!/bin/bash
set -eu -o pipefail
exec solr-foreground -Dlog4j.configurationFile=/opt/solr/server/resources/log4j2-console.xml
