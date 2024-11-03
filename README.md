# Addon_API_Mikrotik
Esse Addon se conecta via API ao RouterOS Mikrotik, podendo puxar algumas informações do Mikrotik para mk-auth.

vou deixar uma versao inicial " Mikrotik-Versao-0.01 " para fazer consulta no RouterOS, para fim de estudo.

Wiki MIkrotik da API

https://help.mikrotik.com/docs/display/ROS/REST+API
--------------------------------------------------------------------------------------------------

Certificado da API

https://youtu.be/dwEcUa2KXNc

certificate add name=local-root-cert common-name=local-cert key-usage=key-cert-sign,crl-sign

certificate sign local-root-cert

certificate add name=webfig common-name=192.168.88.1

certificate sign webfig

ip service set www-ssl certificate=webfig disabled=no
