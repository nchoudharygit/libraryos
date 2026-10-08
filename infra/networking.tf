# # ==========================================
# # 1. NETWORKING (VPC & SUBNETS)
# # ==========================================

# resource "aws_vpc" "libraryos_vpc" {
#   cidr_block = "10.0.0.0/16"
#   tags = {
#     Name = "libraryos-vpc"
#   }
#   enable_dns_support   = true
#   enable_dns_hostnames = true
  
# }

# resource "aws_internet_gateway" "libraryos_igw" {
#   vpc_id = aws_vpc.libraryos_vpc.id
#   tags = {
#     Name = "libraryos-igw"
#   }
# }

# resource "aws_subnet" "libraryos_subnet_public_1" {
#   vpc_id     = aws_vpc.libraryos_vpc.id
#   cidr_block = "10.0.1.0/24"
#   availability_zone = "ap-south-1a"
#   tags = {
#     Name = "libraryos-subnet-public-1"
#   }
#   map_public_ip_on_launch = true
# }

# resource "aws_subnet" "libraryos_subnet_public_2" {
#   vpc_id     = aws_vpc.libraryos_vpc.id
#   cidr_block = "10.0.2.0/24"
#   availability_zone = "ap-south-1b"
#   tags = {
#     Name = "libraryos-subnet-public-2"
#   }
#   map_public_ip_on_launch = true
# }

# resource "aws_route_table" "libraryos_public_rt" {
#   vpc_id = aws_vpc.libraryos_vpc.id
#   route {
#     cidr_block = "0.0.0.0/0"
#     gateway_id = aws_internet_gateway.libraryos_igw.id
#   }
#   tags = {
#     Name = "libraryos-public-rt"
#   }
# }

# resource "aws_route_table_association" "libraryos_public_rt_assoc_1" {
#   subnet_id      = aws_subnet.libraryos_subnet_public_1.id
#   route_table_id = aws_route_table.libraryos_public_rt.id
# }

# resource "aws_route_table_association" "libraryos_public_rt_assoc_2" {
#   subnet_id      = aws_subnet.libraryos_subnet_public_2.id
#   route_table_id = aws_route_table.libraryos_public_rt.id
# }

# # ==========================================
# # 2. SECURITY GROUPS
# # ==========================================

# resource "aws_security_group" "libraryos_alb_sg" {
#   name        = "libraryos-alb-sg"
#   description = "Security group for LibraryOS - allows HTTP traffic from internet to ALB"
#   vpc_id      = aws_vpc.libraryos_vpc.id

#   ingress {
#     from_port   = 80
#     to_port     = 80
#     protocol    = "tcp"
#     cidr_blocks = ["0.0.0.0/0"]
#   }
#   egress {
#     from_port   = 0
#     to_port     = 0
#     protocol    = "-1"
#     cidr_blocks = ["0.0.0.0/0"]
#   }
# }

# # resource "aws_security_group" "libraryos_ecs_sg" {
# #   name        = "libraryos-ecs-sg"
# #   description = "Allows traffic from ALB to ECS tasks"
# #   vpc_id      = aws_vpc.libraryos_vpc.id

# #   ingress {
# #     from_port       = 80
# #     to_port         = 80
# #     protocol        = "tcp"
# #     security_groups = [aws_security_group.libraryos_alb_sg.id]
# #   }
# #   egress {
# #     from_port   = 0
# #     to_port     = 0
# #     protocol    = "-1"
# #     cidr_blocks = ["0.0.0.0/0"]
# #   }

# #   tags = {
# #     Name = "libraryos-ecs-sg"
# #   }
# # }