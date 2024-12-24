## Overview

The deployment.yml file is the one being used to deploy the application to the various environments.The deployment is trigerred when there is a change in the develop and main branches only, ideally which are the test and production env. this code can be further enhance by adding environment option so as the secrets can be read based on the environments added on github.

It consists of varioius jobs:
- **Build-and-push**:
    First we check out the code then login to dockerhub, which we will use to store our docker image. Then we build the dockerfile using the docker/build-push action
- **deploy**:
    This is the job that push our changes to our env. We copy our deployment files from the repository to our remote server using scp. This action has been simplified by appleboy scp action. Then we login to the remote server using appleboy ssh action. onced logged in, we swicth to the directory where we stored our kuberntes files and update our app