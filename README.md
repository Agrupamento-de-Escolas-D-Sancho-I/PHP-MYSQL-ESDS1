PHPMyAdmin: Porta 8081
Login:	root
Pass: 	mariadb

Iniciar serviço apache (Executar no terminal):

sudo sed -i 's/Listen 80$//' /etc/apache2/ports.conf

sudo sed -i 's/<VirtualHost *:80>/ServerName 127.0.0.1\n<VirtualHost *:8080>/' /etc/apache2/sites-enabled/000-default.conf

apache2ctl start
