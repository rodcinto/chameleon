#!/bin/bash
stackFile=./stack.txt
if [ ! -f "$stackFile" ]; then
    echo "File 'stack.txt' not found. Please create it and inform which containers should be in the Stack."
    exit 1
fi

export CHAMELEON_STACK=($(cat $stackFile));

fileBuffer=''
for file in ${CHAMELEON_STACK[@]}; do
    fileBuffer="$fileBuffer -f $file"
done

docker-compose $fileBuffer $*

exit 0
