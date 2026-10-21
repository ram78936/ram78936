variable "aws_region" {
  description = "AWS region for the student demonstration infrastructure"
  type        = string
  default     = "ap-south-1"
}
variable "vpc_id" {
  description = "Existing VPC ID"
  type        = string
}
variable "subnet_id" {
  description = "Existing subnet ID"
  type        = string
}
variable "ami_id" {
  description = "Valid Linux AMI ID for the selected region"
  type        = string
}
variable "ssh_cidr" {
  description = "Your public IP as a single /32 CIDR; do not use 0.0.0.0/0"
  type        = string
  validation {
    condition     = can(cidrhost(var.ssh_cidr, 0))
    error_message = "ssh_cidr must be a valid CIDR block. Prefer a single-IP /32 block."
  }
}
