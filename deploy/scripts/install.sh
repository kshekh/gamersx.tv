#! /bin/sh
cd /var/gamersx/codedeploy

if [ "$DEPLOYMENT_GROUP_NAME" = "gamersx-prod-dg" ]; then
   ENV_NAME=prod
elif [ "$DEPLOYMENT_GROUP_NAME" = "gamersx-demo-dg" ]; then
   ENV_NAME=demo
elif [ "$DEPLOYMENT_GROUP_NAME" = "gamersx-dev-dg" ]; then
   ENV_NAME=dev
else
   echo "Unknown deployment group doesn't match an environment"
   exit -1
fi

DEPLOY_DIR="/var/gamersx/$ENV_NAME/code"
ENV_DIR="/var/gamersx/$ENV_NAME/env"

# Clear out last deployment
rm -rf $DEPLOY_DIR

# Copy fresh code
cp -r . $DEPLOY_DIR

# Copy our environment and link files
cp $ENV_DIR/.env* $DEPLOY_DIR

mkdir $DEPLOY_DIR/var

ln -sf $ENV_DIR/vendor $DEPLOY_DIR
ln -sf $ENV_DIR/themes $DEPLOY_DIR/public/images
ln -sf $ENV_DIR/items $DEPLOY_DIR/public/images

ln -sf $ENV_DIR/cache $DEPLOY_DIR/var
ln -sf $ENV_DIR/log $DEPLOY_DIR/var
ln -sf $ENV_DIR/data.db $DEPLOY_DIR/var

cd $DEPLOY_DIR && composer install
