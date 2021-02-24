## Installing EB command on Windows

- Reference -> https://docs.aws.amazon.com/elasticbeanstalk/latest/dg/eb-cli3-install-virtualenv.html
- pip install --user virtualenv
- virtualenv ~/eb-ve 
- ~\eb-ve\Scripts\activate
- pip install awsebcli --upgrade

Activate the virtual environment with this command to run EB commands - ~\eb-ve\Scripts\activate

## Configuring SSH
- Create a SSH key in the console
- Then `eb ssh --setup`
- cp ForkTheCoopPem.pem ~/.ssh/
- chmod 0400 ~/.ssh/ForkTheCoopPem.pem
- ssh -i "ForkTheCoopPem.pem" ec2-user@ec2-35-178-168-145.eu-west-2.compute.amazonaws.com

## Docker note
Deleting all the images.
- docker rmi -f $(docker images -a -q)
Removing docker volume
- docker volume rm -f fork-data
