# resource "aws_ecs_cluster" "libraryos_ecs_cluster" {
#   name = "libraryos-ecs-cluster"

#   setting {
#     name = "containerInsights"
#     value = "enabled"
#   }

# }

# resource "aws_cloudwatch_log_group" "libraryos_catalog" {
#   name              = "/ecs/libraryos-catalog"
#   retention_in_days = 7
      
# }

# ### ecs task definition for catalog service
# resource "aws_ecs_task_definition" "libraryos_catalog" {
#     family = "libraryos-catalog"
#     requires_compatibilities = ["FARGATE"]
#     network_mode = "awsvpc"
#     cpu = "256"
#     memory = "512"
#     execution_role_arn = aws_iam_role.libraryos_ecs_task_execution_role.arn

#     container_definitions = jsonencode([
#         ###catalog_fpm and nginx containers
#         {
#             name = "catalog_fpm"
#             image = "162185499985.dkr.ecr.ap-south-1.amazonaws.com/libraryos-catalog-ecr:latest"
#             essential = true
#             portMappings = [
#                 {
#                     containerPort = 9000
#                     hostPort = 9000
#                     protocol = "tcp"
#                 }
#             ]
#         },
#         {
#             name = "catalog_nginx"
#             image = "162185499985.dkr.ecr.ap-south-1.amazonaws.com/libraryos-catalog-nginx-ecr:latest"
#             essential = true
#             portMappings = [
#                 {
#                     containerPort = 80
#                     hostPort = 80
#                     protocol = "tcp"
#                 }
#             ]
#         }
#     ])
# }

# ## ecs service for catalog service
# resource "aws_ecs_service" "libraryos_catalog" {
#     name = "libraryos-catalog-service"
#     cluster = aws_ecs_cluster.libraryos_ecs_cluster.id
#     task_definition = aws_ecs_task_definition.libraryos_catalog.arn
#     desired_count = 1
#     launch_type = "FARGATE"

#      network_configuration {
#         subnets = [ aws_subnet.libraryos_subnet_public_1.id, aws_subnet.libraryos_subnet_public_2.id ]
#         security_groups = [aws_security_group.libraryos_ecs_sg.id]
#         assign_public_ip = true
# }
# }