# ecr for catalog microservice image
resource "aws_ecr_repository" "libraryos_catalog_ecr" {
    name                 = "libraryos-catalog-ecr"
    image_tag_mutability = "IMMUTABLE" 
    image_scanning_configuration {
        scan_on_push = true
    }
    force_delete = true
    

}
 resource "aws_ecr_lifecycle_policy" "libraryos_catalog_ecr_lifecycle_policy" {
    repository = aws_ecr_repository.libraryos_catalog_ecr.name
    policy     = jsonencode({
        rules = [
            {
                rulePriority = 1
                description  = "Expire untagged images older than 30 days"
                selection    = {
                    tagStatus    = "untagged"
                    countType    = "sinceImagePushed"
                    countUnit    = "days"
                    countNumber  = 30
                }
                action       = {
                    type = "expire"
                }
            }
        ]
    })
}

output "libraryos_catalog_ecr_repository_url" {
    value = aws_ecr_repository.libraryos_catalog_ecr.repository_url
}
 ## ecr for nginx image
resource "aws_ecr_repository" "libraryos_catalog_nginx_ecr" {
  name                 = "libraryos-catalog-nginx-ecr"
  image_tag_mutability = "IMMUTABLE"
  force_delete         = true

  image_scanning_configuration {
    scan_on_push = true
  }
}
resource "aws_ecr_lifecycle_policy" "libraryos_catalog_nginx_ecr_lifecycle_policy" {
    repository = aws_ecr_repository.libraryos_catalog_nginx_ecr.name
    policy     = jsonencode({
        rules = [
            {
                rulePriority = 1
                description  = "Expire untagged images older than 30 days"
                selection    = {
                    tagStatus    = "untagged"
                    countType    = "sinceImagePushed"
                    countUnit    = "days"
                    countNumber  = 30
                }
                action       = {
                    type = "expire"
                }
            }
        ]
    })
}
output "libraryos_catalog_nginx_ecr_repository_url" {
    value = aws_ecr_repository.libraryos_catalog_nginx_ecr.repository_url
}