### s3 bucket for remote state
terraform {
  backend "s3" {
    bucket = "libraryos-terraform-state-162185499985"
    key    = "libraryos-terraform-state/terraform.tfstate"
    region = "ap-south-1"
    use_lockfile = true
    encrypt      = true
  }
}