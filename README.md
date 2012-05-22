LBCMail
======

Système d'alerte mail pour Leboncoin.fr

## Introduction

Cette application web permet d'alerter par mail lorsque des nouvelles annonces sur Leboncoin.fr sont publiées.

## Installation

L'installation de l'application est très simple.
Le plus complexe est de trouver un hébergement permettant de récupérer des informations distantes.
J'ai inclus une détection dans l'application, vous serez donc informé si oui ou non votre hébergement permet de faire fonctionner correctement l'application.

Une fois votre hébergement trouvé, envoyez simplement le contenu du fichier téléchargé (après décompression) sur votre espace web. Vous pouvez sans problème le mettre dans un sous dossier.

**Donnez les permissions d'écriture sur le répertoire "config". Celui-ci contiendra la liste de vos alertes, il faut donc pouvoir écrire le fichier.**

## Utilisation

Encore une fois, c'est très simple.

Rendez-vous à l'adresse de l'application. Vous pouvez ajouter votre première alerte en cliquant sur le lien "Ajouter une alerte".

* **E-Mail** : entrez l'E-Mail destinataire des alertes.
* **Titre** : un titre. 
* **Url de recherche** : c'est l'adresse Leboncoin correspondant à votre recherche. 
* **Intervalle de contrôle d'alerte** : l'alerte sera contrôle par le script toutes les X minutes. 

Maintenant, il faut définir une tâche cron. Deux solutions s'offrent à vous :

* appeler directement le fichier "check.php" en CLI :
`*/5 * * * * php -f /path/to/your/web/directory/check.php`
* ou appeler "check.php" via l'adresse de votre site. Exemple : http://exemple.com/alerte/check.php

Pour le second point, vous pouvez utiliser un service en ligne appelé webcron (voir dans un moteur de recherche).


## Pour finir

Surveillez bien les mises à jour de l'application. Lorsque Leboncoin effectue des modifications sur leur site, l'application risque de ne plus fonctionner. En général, j'applique un correctif dès que je suis mis au courant.
