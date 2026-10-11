data "aws_vpc" "default" {
  default = true
}

data "aws_subnets" "default_public" {
    filter {
        name   = "vpc-id"
        values = [data.aws_vpc.default.id]
    }
    filter {
        name   = "default-for-az"
        values = ["true"]
    }
}

data "aws_subnet" "public_subnet_1" {
    filter {
        name   = "vpc-id"
        values = [data.aws_vpc.default.id]
    }
    filter {
        name   = "default-for-az"
        values = ["true"]
    }
    filter {
        name   = "availability-zone"
        values = ["ap-south-1a"]
    }
}

data "aws_subnet" "public_subnet_2" {
    filter {
        name   = "vpc-id"
        values = [data.aws_vpc.default.id]
    }
    filter {
        name   = "default-for-az"
        values = ["true"]
    }
    filter {
        name   = "availability-zone"
        values = ["ap-south-1b"]
    }
}


locals {
    default_public_subnet_ids = [
        data.aws_subnet.public_subnet_1.id,
        data.aws_subnet.public_subnet_2.id
    ]
}


resource "aws_security_group" "libraryos_ecs_service_sg" {
    name        = "libraryos-ecs-service-sg"
    description = "Security group for libraryos ECS service"
    vpc_id      = data.aws_vpc.default.id

    ingress {
        from_port   = 80
        to_port     = 80
        protocol    = "tcp"
        cidr_blocks = ["103.197.74.99/32"]
    }
    egress {
        from_port   = 0
        to_port     = 0
        protocol    = "-1"
        cidr_blocks = ["0.0.0.0/0"]
    }
}

resource "aws_ecs_cluster" "libraryos_ecs_cluster" {
  name = "libraryos-ecs-cluster"

  setting {
    name = "containerInsights"
    value = "enabled"
  }

}

resource "aws_cloudwatch_log_group" "libraryos_catalog" {
  name              = "/ecs/libraryos-catalog"
  retention_in_days = 7
      
}

### ecs task definition for catalog service
resource "aws_ecs_task_definition" "libraryos_catalog" {
    family = "libraryos-catalog"
    requires_compatibilities = ["FARGATE"]
    network_mode = "awsvpc"
    cpu = "256"
    memory = "512"
    execution_role_arn = aws_iam_role.libraryos_ecs_task_execution_role.arn

    container_definitions = jsonencode([
        ###catalog_fpm and nginx containers
        {
            name = "catalog_fpm"
            image = "162185499985.dkr.ecr.ap-south-1.amazonaws.com/libraryos-catalog-ecr:${var.image_tag}"
            essential = true
            portMappings = [
                {
                    containerPort = 9000
                    hostPort = 9000
                    protocol = "tcp"
                }
            ]
            logConfiguration = {
                logDriver = "awslogs"
                options = {
                    awslogs-group         = aws_cloudwatch_log_group.libraryos_catalog.name
                    awslogs-region        = "ap-south-1"
                    awslogs-stream-prefix = "libraryos-catalog"
                }
            }
        },
        {
            name = "catalog_nginx"
            image = "162185499985.dkr.ecr.ap-south-1.amazonaws.com/libraryos-catalog-nginx-ecr:${var.image_tag}"
            essential = true
            logConfiguration = {
                logDriver = "awslogs"
                options = {
                    awslogs-group         = aws_cloudwatch_log_group.libraryos_catalog.name
                    awslogs-region        = "ap-south-1"
                    awslogs-stream-prefix = "libraryos-catalog-nginx"
                }
            }
            portMappings = [
                {
                    containerPort = 80
                    hostPort = 80
                    protocol = "tcp"
                }
            ]
        }
    ])
}

## ecs service for catalog service
resource "aws_ecs_service" "libraryos_catalog" {
    name = "libraryos-catalog-service"
    cluster = aws_ecs_cluster.libraryos_ecs_cluster.id
    task_definition = aws_ecs_task_definition.libraryos_catalog.arn
    desired_count = 1
    launch_type = "FARGATE"

    network_configuration {
        subnets = local.default_public_subnet_ids
        assign_public_ip = true
        security_groups = [aws_security_group.libraryos_ecs_service_sg.id]
    }
}