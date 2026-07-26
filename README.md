<img width="294" height="124" alt="image" src="https://github.com/user-attachments/assets/959a3ced-48fc-4b1e-89b5-69701476b16d" /># aws-infraestructure-version-2-
Integracion de Docker | Terraform | + CI CD | GitHub Actions 

Dos contenedores con Docker Compose: la app PHP y MySQL 8, reproduciendo en local el par EC2 + RDS. La base de datos no expone puertos (como una RDS privada) y la app espera a su healthcheck antes de arrancar. 
<img width="1091" height="98" alt="image" src="https://github.com/user-attachments/assets/e788e235-827c-4d91-b459-f49aee4b34a8" />


<img width="1628" height="344" alt="image" src="https://github.com/user-attachments/assets/50026b0b-1061-432e-838d-7f228dc8e915" />




Fase 2 — Infraestructura en AWS con Terraform
La misma arquitectura de la Fase 1, pero llevada a AWS con Terraform (Infraestructura como Código): en vez de crearla a mano en la consola, la describimos en ficheros .tf y Terraform la crea (o destruye) con un solo comando.

                 Internet
                    │  Internet Gateway
              ┌─────▼─────┐  VPC 10.2.0.0/16
   pública ──►│    EC2    │  Docker + app PHP · puerto 80
              └─────┬─────┘
                    │ 3306
   privada ──►┌─────▼─────┐  RDS MySQL 8 · sin acceso desde Internet
              └───────────┘
La EC2 es la única que puede hablar con la RDS (puerto 3306); la base de datos no es accesible desde Internet.

Estructura de terraform/
Fichero / carpeta	Qué hace
main.tf	Orquesta los 3 módulos y les pasa las variables
providers.tf	Proveedor AWS, región y versión de Terraform
variables.tf / outputs.tf	Entradas (nombres, credenciales…) y salidas (IP, endpoint RDS)
terraform.tfvars.example	Plantilla de variables sin secretos reales
modules/network/	Red: VPC, subredes públicas/privadas (2 AZ), Internet Gateway, rutas y security groups (firewall: 80 abierto, 22 solo desde tu IP, 3306 solo desde la web)
modules/compute/	Máquina: EC2 Ubuntu (t3.micro) + user_data.sh.tpl, el script de arranque que instala Docker, clona el repo y levanta el contenedor de la Fase 1 apuntando a la RDS
modules/database/	Base de datos: RDS MySQL 8 (db.t3.micro) privada, en las subredes privadas
Seguridad: las contraseñas y el terraform.tfstate nunca se suben al repo (van en .gitignore), porque contienen datos sensibles.

Los 4 pasos del despliegue
1. Escribir el código — la infraestructura descrita en los .tf. Aún no existe nada en AWS.

2. Crear en AWS — terraform apply construye los recursos reales (~5-10 min por la RDS) y muestra los outputs (IP pública y endpoint RDS):

cd terraform && terraform init && terraform apply
<!-- 📸 CAPTURA 1: final del `apply` con los outputs -->
3. Comprobar — abrir http://<IP-pública> (carga el login del inventario) y ver la EC2 y la RDS en la consola de AWS (región eu-west-1).

<!-- 📸 CAPTURA 2: app en el navegador con la IP de AWS visible --> <!-- 📸 CAPTURA 3: consola AWS → EC2 "running" en eu-west-1 --> <!-- 📸 CAPTURA 4: consola AWS → RDS "Available" en eu-west-1 -->
4. Eliminar todo — como AWS cobra por hora, al terminar se destruye la infraestructura completa:

cd terraform && terraform destroy
<!-- 📸 CAPTURA 5 (opcional): consola AWS vacía tras el destroy -->
Código .tf  →  apply  →  Infra REAL en AWS  →  verificar  →  destroy
(describes)    (crea)     (existe y cobra)      (capturas)    (borra todo)

