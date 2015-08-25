#! /bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd)"
NAME=$1
COMMAND=$2
ARGS=$3

FILEPATH=$DIR/../app/cache/$NAME

lockrun --lockfile=$FILEPATH -- $COMMAND --env=prod $ARGS
