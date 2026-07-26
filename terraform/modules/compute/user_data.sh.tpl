#!/bin/bash
# Arranque de la EC2: Docker + app de la Fase 1 contra RDS
# Log completo en /var/log/user-data.log (útil para depurar)
exec > /var/log/user-data.log 2>&1
set -x

export DEBIAN_FRONTEND=noninteractive
apt-get update -y
apt-get install -y docker.io git mysql-client-core-8.0
systemctl enable --now docker

# Clonar el repositorio con la app dockerizada (Fase 1)
git clone ${repo_url} /opt/app
cd /opt/app/docker

# Esperar a que RDS acepte conexiones
until mysql -h ${db_host} -u ${db_user} -p'${db_password}' -e "SELECT 1" >/dev/null 2>&1; do
  echo "Esperando a RDS..."
  sleep 10
done

# Cargar el esquema y los datos iniciales (mismo init.sql de la Fase 1)
mysql -h ${db_host} -u ${db_user} -p'${db_password}' < db/init.sql

# Fijar una contraseña conocida para Administrador, independientemente de los
# hashes desconocidos que traiga el init.sql del repo. (\$ escapado para bash)
mysql -h ${db_host} -u ${db_user} -p'${db_password}' ${db_name} -e \
  "UPDATE t_usuario SET usuario_clave='\$2y\$12\$KByVoQ4iO5nkxTUbI6Pwz.rA94FdXIaHU464ZiU8Zj9kzA2OzY5aS' WHERE usuario_usuario='Administrador';"

# Construir la imagen y arrancar el contenedor apuntando a RDS
docker build -t academia-app ./app
docker run -d --name academia-app --restart unless-stopped -p 80:80 \
  -e DB_HOST=${db_host} \
  -e DB_NAME=${db_name} \
  -e DB_USER=${db_user} \
  -e DB_PASSWORD='${db_password}' \
  academia-app

echo "Despliegue completado"
