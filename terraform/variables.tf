variable "aws_region" {
  description = "Región AWS por defecto"
  type        = string
  default     = "eu-west-1"
}

variable "project_name" {
  description = "Nombre del proyecto (para tags y nombres de recursos)"
  type        = string
  default     = "retacantabria-v2"
}

variable "repo_url" {
  description = "Repositorio público con la app dockerizada (Fase 1)"
  type        = string
  default     = "https://github.com/albert0fernandez/aws-infraestructure-version-2-.git"
}

variable "admin_cidr" {
  description = "Tu IP pública en formato CIDR para acceso SSH (ej. 1.2.3.4/32)"
  type        = string
}

variable "instance_type" {
  description = "Tipo de instancia EC2 (elegible Free Tier)"
  type        = string
  default     = "t3.micro"
}

variable "key_name" {
  description = "Nombre del key pair EC2 existente para SSH (opcional)"
  type        = string
  default     = null
}

variable "db_name" {
  description = "Nombre de la base de datos"
  type        = string
  default     = "pdo_solucion"
}

variable "db_user" {
  description = "Usuario maestro de RDS"
  type        = string
  default     = "userCRUD"
}

variable "db_password" {
  description = "Contraseña de RDS (pásala por terraform.tfvars o TF_VAR_db_password, nunca al repo)"
  type        = string
  sensitive   = true
}
