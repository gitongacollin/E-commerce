provider "aws" {
    region = "us-east-1"
    default_tags {
        tags = {
            Environment = "Test"
            Project     = "WebApp"
            Terraform   = "true"
        }
    }
}
module "vpc" {
    source = "terraform-aws-modules/vpc/aws"
    version = "5.1.2"

    name = "app-vpc"
    cidr = "10.0.0.0/16"

    azs = ["us-east-1a", "us-east-1b"]
    public_subnets = ["10.0.1.0/24", "10.0.2.0/24"]
    private_subnets = ["10.0.3.0/24", "10.0.4.0/24"]
    enable_nat_gateway     = true
    single_nat_gateway     = false
    one_nat_gateway_per_az = true

    enable_dns_hostnames = true
    enable_dns_support   = true
}
resource "aws_security_group" "app_sg" {
    name_prefix = "app-sg"
    vpc_id = module.vpc.vpc_id

    ingress {
        description = "Allow HTTP"
        from_port = 80
        to_port = 80
        protocol = "tcp"
        cidr_blocks = ["0.0.0.0/0"]
    }
    ingress {
        description = "Allow HTTPS"
        from_port = 443
        to_port = 443
        protocol = "tcp"
        cidr_blocks = ["0.0.0.0/0"]
    }
    ingress {
        description = "Kubernetes NodePort Range"
        from_port = 30000
        to_port = 32767
        protocol = "tcp"
        cidr_blocks = ["0.0.0.0/0"]
    }
    ingress {
        description = "SSH Access"
        from_port = 22
        to_port = 22
        protocol = "tcp"
        cidr_blocks = ["0.0.0.0/0"]
    }
    egress {
        from_port = 0
        to_port = 0
        protocol = "-1"
        cidr_blocks = ["0.0.0.0/0"]
    }
}
resource "aws_security_group" "db_sg" {
    name_prefix = "db-sg"
    vpc_id = module.vpc.vpc_id

    ingress {
        description = "Allow MYSQL access from web server"
        from_port = 3306
        to_port = 3306
        protocol = "tcp"
        security_groups = [aws_security_group.app_sg.id]
    }
    egress {
        from_port = 0 
        to_port = 0
        protocol = "-1"
        cidr_blocks = ["0.0.0.0/0"]
    }
}
resource "aws_launch_template" "app_template" {
    name_prefix = "web-server"
   
    image_id = 	"ami-079cb33ef719a7b78"
    instance_type = "t2.micro"

    key_name = "test2"

    network_interfaces {
        security_groups = [aws_security_group.app_sg.id]
        subnet_id = module.vpc.public_subnets[0]
    }

    user_data = <<-EOF
                #!/bin/bash
                sudo apt-get update -y
                sudo apt-get install -y apt-transport-https ca-certificates curl software-properties-common

                curl -fsSL https://get.docker.com -o get-docker.sh
                sudo sh get-docker.sh
                sudo usermod -aG docker ubuntu
                sudo systemctl enable docker
                sudo systemctl start docker

                # Install kubectl
                curl -LO "https://dl.k8s.io/release/$(curl -L -s https://dl.k8s.io/release/stable.txt)/bin/linux/amd64/kubectl"
                sudo install -o root -g root -m 0755 kubectl /usr/local/bin/kubectl

                curl -LO https://storage.googleapis.com/minikube/releases/latest/minikube-linux-amd64
                sudo install minikube-linux-amd64 /usr/local/bin/minikube

                sudo -u ubuntu minikube start --driver=docker
                echo 'eval $(minikube docker-env)' >> /home/ubuntu/.bashrc

                
                sudo apt-get install -y git
                sudo chown -R ubuntu:ubuntu /home/ubuntu/.kube
                sudo chown -R ubuntu:ubuntu /home/ubuntu/.minikube

                # Install docker-compose
                sudo curl -L "https://github.com/docker/compose/releases/download/v2.20.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
                sudo chmod +x /usr/local/bin/docker-compose
                
                sudo apt-get install -y apt-transport-https ca-certificates curl
                sudo curl -fsSL https://packages.cloud.google.com/apt/doc/apt-key.gpg | sudo apt-key add -
                echo "deb https://apt.kubernetes.io/ kubernetes-xenial main" | sudo tee -a /etc/apt/sources.list.d/kubernetes.list
                sudo apt-get update -y
                sudo apt-get install -y kubectl

                kubectl version --client
                EOF
}

resource "aws_autoscaling_group" "app_asg" {
    launch_template {
        id  = aws_launch_template.app_template.id
        version = "$Latest"
    }

    min_size = 2
    max_size = 5
    desired_capacity = 2
    vpc_zone_identifier = module.vpc.public_subnets
    target_group_arns = [aws_lb_target_group.app_tg.arn]

    tag {
        key  = "Name"
        value = "app-server"
        propagate_at_launch = true
    }
}
resource "aws_autoscaling_policy" "scale_out"{
    name = "scale_out-policy"
    scaling_adjustment = 1
    adjustment_type = "ChangeInCapacity"
    autoscaling_group_name = aws_autoscaling_group.app_asg.name
    cooldown = 300
}
resource "aws_autoscaling_policy" "scale_in" {
    name = "scale_in-policy"
    scaling_adjustment = -1
    adjustment_type =  "ChangeInCapacity"
    autoscaling_group_name = aws_autoscaling_group.app_asg.name
    cooldown = 300
}
resource "aws_cloudwatch_metric_alarm" "high_cpu" {
    alarm_name = "high-cpu-alar"
    comparison_operator = "GreaterThanThreshold"
    evaluation_periods = 2
    metric_name = "CPUUtilization"
    namespace = "AWS/EC2"
    period = 60
    statistic = "Average"
    threshold = 80
    alarm_actions = [aws_autoscaling_policy.scale_out.arn]
    dimensions = {
        AutoScalingGroupName = aws_autoscaling_group.app_asg.name
    }
}
resource "aws_cloudwatch_metric_alarm" "low_cpu" {
    alarm_name = "low-cpu-alarm"
    comparison_operator = "LessThanThreshold"
    evaluation_periods = 2
    metric_name = "CPUUtilization"
    namespace = "AWS/EC2"
    period = 60
    statistic = "Average"
    threshold =  30
    alarm_actions = [aws_autoscaling_policy.scale_in.arn]
    dimensions = {
        AutoScalingGroupName = aws_autoscaling_group.app_asg.name
    }
}

resource "aws_lb" "app_lb" {
    name = "app-lb"
    internal = false
    load_balancer_type = "application"
    security_groups = [aws_security_group.app_sg.id]
    subnets = module.vpc.public_subnets
}

resource "aws_lb_target_group" "app_tg" {
    name = "app-tg"
    port = 80
    protocol = "HTTP"
    vpc_id = module.vpc.vpc_id
}

module "rds" {
    source = "terraform-aws-modules/rds/aws"
    version = "6.1.1"

    identifier = "app-db"
    engine = "mysql"
    engine_version = "8.0"
    family = "mysql8.0"
    major_engine_version = "8.0"
    instance_class = "db.t3.micro"
    allocated_storage = 20
    username = "admin"
    password = "myadmin"
    vpc_security_group_ids = [aws_security_group.db_sg.id]
    subnet_ids = module.vpc.private_subnets
    db_subnet_group_name = module.vpc.database_subnet_group_name
}
output "load_balancer_dns" {
    description = "DNS name of the load balancer"
    value       = aws_lb.app_lb.dns_name
}

output "rds_endpoint" {
    description = "RDS instance endpoint"
    value       = module.rds.db_instance_endpoint
}

output "vpc_id" {
    description = "VPC ID"
    value       = module.vpc.vpc_id
}
