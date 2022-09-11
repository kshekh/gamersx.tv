#!/bin/bash

env=$1
region=$2
pull_request_id=$3
action=$4

cd iac/regions/$region/$env/apps/pipelines/

cp -r sample pr-$pull_request_id

cd pr-$pull_request_id

TF_VAR_env=$env terragrunt $action -var="app=pr-$pull_request_id" -auto-approve -lock=false
