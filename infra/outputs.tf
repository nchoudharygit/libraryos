# ==========================================
# 1. ECR REPOSITORY URLS (For Docker Push)
# ==========================================

output "app_ecr_url" {
  description = "The ECR repository URL for the main Laravel/PHP application image"
  value       = aws_ecr_repository.libraryos_catalog_ecr.repository_url
}

output "nginx_ecr_url" {
  description = "The ECR repository URL for the companion Nginx web server image"
  value       = aws_ecr_repository.libraryos_catalog_nginx_ecr.repository_url
}

# ==========================================
# 2. ACCESSIBILITY / COMMAND ASSISTANCE
# ==========================================

output "ecr_login_command" {
  description = "Convenience command to authenticate your local Docker daemon with AWS ECR"
  value       = "aws ecr get-login-password --region us-east-1 | docker login --username AWS --password-stdin ${split("/", aws_ecr_repository.libraryos_catalog_ecr.repository_url)[0]}"
}

# output "ecs_cluster_name" {
#   description = "The name of your deployed ECS Cluster"
#   value       = aws_ecs_cluster.libraryos_cluster.name
# }

# output "ecs_service_name" {
#   description = "The name of your deployed ECS Service"
#   value       = aws_ecs_service.libraryos_catalog_service.name
# }
