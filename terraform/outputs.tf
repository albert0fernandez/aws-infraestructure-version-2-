output "app_url" {
  description = "URL de la aplicación (dale ~3-4 min tras el apply para que user_data termine)"
  value       = "http://${module.compute.public_ip}"
}

output "ec2_public_ip" {
  description = "IP pública de la instancia EC2"
  value       = module.compute.public_ip
}

output "rds_endpoint" {
  description = "Endpoint de la base de datos RDS"
  value       = module.database.db_endpoint
}
