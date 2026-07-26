output "db_endpoint" {
  description = "Hostname de la RDS (sin puerto)"
  value       = aws_db_instance.main.address
}
