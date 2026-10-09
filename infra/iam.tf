resource "aws_iam_role" "libraryos_ecs_task_execution_role" {
  name = "libraryos-ecs-task-execution-role"

  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Action = "sts:AssumeRole"
        Effect = "Allow"
        Principal = {
          Service = "ecs-tasks.amazonaws.com"
        }
      },
    ]
  })
} 

resource "aws_iam_role_policy_attachment" "libraryos_ecs_task_execution_role_policy_attachment" {
  role       = aws_iam_role.libraryos_ecs_task_execution_role.name
  policy_arn = "arn:aws:iam::aws:policy/service-role/AmazonECSTaskExecutionRolePolicy"
}