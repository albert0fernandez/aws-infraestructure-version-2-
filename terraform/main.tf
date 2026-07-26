# ──────────────────────────────────────────────────────────────
# RetaCantabria v2 — Fase 2
# Misma arquitectura que la v1 (CloudFormation), ahora en Terraform:
# VPC → RDS privada → EC2 que ejecuta el contenedor Docker de la Fase 1
# ──────────────────────────────────────────────────────────────

module "network" {
  source = "./modules/network"

  project_name = var.project_name
  vpc_cidr     = "10.2.0.0/16"
  admin_cidr   = var.admin_cidr
}

module "database" {
  source = "./modules/database"

  project_name       = var.project_name
  private_subnet_ids = module.network.private_subnet_ids
  db_sg_id           = module.network.db_sg_id
  db_name            = var.db_name
  db_user            = var.db_user
  db_password        = var.db_password
}

module "compute" {
  source = "./modules/compute"

  project_name     = var.project_name
  public_subnet_id = module.network.public_subnet_ids[0]
  web_sg_id        = module.network.web_sg_id
  instance_type    = var.instance_type
  key_name         = var.key_name
  repo_url         = var.repo_url

  db_host     = module.database.db_endpoint
  db_name     = var.db_name
  db_user     = var.db_user
  db_password = var.db_password
}
